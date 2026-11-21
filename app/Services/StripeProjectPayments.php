<?php

namespace App\Services;

use App\Models\ProjectPayment;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StripeProjectPayments implements ProjectPaymentProvider
{
    public function session(ProjectPayment $payment): array
    {
        $client = $this->client();
        if ($payment->provider_id) {
            $response = $client->get('payment_intents/'.$payment->provider_id);
        } else {
            $response = $client->withHeader('Idempotency-Key', 'bidfreq-'.$payment->reference)->post('payment_intents', [
                'amount' => self::cents($payment->amount),
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'metadata' => ['payment_reference' => $payment->reference],
            ]);
        }
        if (! $response->successful() || ! is_string($response->json('id')) || ! is_string($response->json('client_secret'))) {
            throw new RuntimeException('Stripe could not prepare the payment.');
        }

        return ['id' => $response->json('id'), 'client_secret' => $response->json('client_secret')];
    }

    public function cancel(ProjectPayment $payment): void
    {
        $id = $payment->provider_id ?? $this->session($payment)['id'];
        $client = $this->client();
        $response = $client->post('payment_intents/'.$id.'/cancel');
        if (! $response->successful() && $client->get('payment_intents/'.$id)->json('status') !== 'canceled') {
            throw new RuntimeException('Stripe has not confirmed cancellation.');
        }
    }

    public function verify(ProjectPayment $payment): array
    {
        $id = $payment->provider_id ?? $this->session($payment)['id'];
        $response = $this->client()->get('payment_intents/'.$id);
        if (! $response->successful() || $response->json('id') !== $id || $response->json('currency') !== 'usd'
            || $response->json('metadata.payment_reference') !== $payment->reference
            || $response->json('amount') !== self::cents($payment->amount)) {
            throw new RuntimeException('Stripe payment details could not be verified.');
        }
        $status = match ($response->json('status')) {
            'succeeded' => 'completed',
            'canceled' => 'cancelled',
            'requires_payment_method' => $response->json('last_payment_error') ? 'failed' : 'pending',
            default => 'pending',
        };
        if ($status === 'completed' && $response->json('amount_received') !== self::cents($payment->amount)) {
            throw new RuntimeException('Stripe received a different amount.');
        }

        return ['status' => $status, 'id' => $id];
    }

    public static function cents(string $amount): int { return BigDecimal::of($amount)->multipliedBy(100)->toInt(); }

    private function client()
    {
        $secret = config('project_payments.stripe.secret');
        if (! is_string($secret) || $secret === '') {
            throw new RuntimeException('Stripe is not configured.');
        }
        return Http::baseUrl('https://api.stripe.com/v1/')->withToken($secret)->asForm()->acceptJson()->withoutRedirecting()->timeout(10);
    }
}
