<?php

namespace App\Services\Estimates;

use App\Models\LibraryTask;
use App\Models\RepairRequest;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use JsonException;
use UnexpectedValueException;

class DraftPricing
{
    public const BASES = ['customer_measurement', 'counted_from_photo', 'calculated_from_customer_measurement', 'cannot_determine'];

    public function decode(string $text): array
    {
        try {
            $data = json_decode($text, false, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new UnexpectedValueException('The Gemini response was not valid JSON.');
        }
        if (! $data instanceof \stdClass) {
            throw new UnexpectedValueException('The Gemini response must be a JSON object.');
        }

        $analysis = [];
        foreach (['complaint', 'cause', 'correction', 'complications', 'uncertainty'] as $field) {
            if (! is_string($data->$field ?? null) || trim($data->$field) === '' || mb_strlen($data->$field) > 5000) {
                throw new UnexpectedValueException('The Gemini response is missing a valid '.$field.'.');
            }
            $analysis[$field] = $data->$field;
        }
        foreach (['unmatched', 'assumptions', 'risk_flags'] as $field) {
            if (! $this->strings($data->$field ?? null)) {
                throw new UnexpectedValueException('The Gemini response is missing a valid '.$field.' list.');
            }
            $analysis[$field] = $data->$field;
        }
        $confidence = $data->confidence ?? null;
        if ((! is_int($confidence) && ! is_float($confidence)) || $confidence < 0 || $confidence > 1
            || ! is_array($data->items ?? null) || count($data->items) > 300) {
            throw new UnexpectedValueException('The Gemini response contains invalid confidence or work items.');
        }
        $analysis['confidence'] = $confidence;

        return [$analysis, $data->items];
    }

    public static function lineTotal(string $quantity, string $rate): BigDecimal
    {
        return BigDecimal::of($quantity)->multipliedBy($rate)->toScale(2, RoundingMode::HalfUp);
    }

    public function calculate(RepairRequest $repair, array $analysis, array $items, ?array $allowedTaskIds = null): array
    {
        $ids = collect($items)->map(fn ($item) => $item->task_id ?? null)
            ->filter(fn ($id) => is_string($id) && strlen($id) <= 32 && ($allowedTaskIds === null || in_array($id, $allowedTaskIds, true)))->unique()->values();
        $tasks = LibraryTask::whereIn('task_id', $ids)->get()->keyBy('task_id');
        $total = BigDecimal::zero()->toScale(2);
        $lines = [];
        foreach ($items as $item) {
            $id = $item->task_id ?? null;
            $unit = $item->unit ?? null;
            $basis = $item->quantity_basis ?? null;
            $evidence = $item->evidence ?? null;
            $task = is_string($id) ? $tasks->get($id) : null;
            $quantity = $this->quantity($item->quantity ?? null);
            $flags = [];
            if (! $task) {
                $flags[] = 'Unmatched Library task';
            } elseif ($unit !== $task->unit) {
                $flags[] = 'Unit does not match the Library';
            }
            if (! in_array($basis, self::BASES, true)) {
                $flags[] = 'Invalid quantity basis';
            }
            if ($basis === 'cannot_determine' || $quantity === null) {
                $flags[] = 'Quantity could not be determined';
                $quantity = null;
            }
            if (! $this->strings($evidence) || $evidence === []) {
                $flags[] = 'Evidence is missing or invalid';
                $evidence = [];
            }
            if ($task && in_array($task->task_id, LibraryTask::AMBIGUOUS, true)) {
                $flags[] = 'Confirm unit and quantity';
            }
            if ($task?->pricing_mode === 'quote_by_scope') {
                $flags[] = 'Quote by scope: Admin rate required';
            }
            $rate = $flags === [] ? $task->bidfreq_standard_rate : null;
            $lineTotal = $rate === null ? null : self::lineTotal($quantity, $rate);
            if ($lineTotal !== null) {
                $total = $total->plus($lineTotal);
            }
            $lines[] = [
                'task_id' => $task?->task_id, 'library_version_id' => $task?->library_version_id,
                'name' => $task?->task_name ?? (is_string($id) ? mb_substr($id, 0, 255) : 'Unmatched work item'),
                'unit' => $task?->unit, 'quantity' => $quantity, 'rate' => $rate,
                'line_total' => $lineTotal === null ? null : (string) $lineTotal,
                'priced_on' => $rate === null ? null : now()->toDateString(), 'source' => 'ai',
                'quantity_basis' => in_array($basis, self::BASES, true) ? $basis : null,
                'evidence' => $evidence, 'flags' => $flags,
            ];
        }
        foreach ($analysis['unmatched'] as $description) {
            $lines[] = ['name' => mb_substr($description, 0, 255), 'source' => 'ai', 'evidence' => [], 'flags' => ['Unmatched work']];
        }

        return [
            'draft' => ['analysis' => $analysis, 'total' => (string) $total, 'over_nte' => $repair->nte !== null && $total->isGreaterThan($repair->nte)],
            'lines' => $lines,
        ];
    }

    public function manual(RepairRequest $repair, array $analysis, array $items): array
    {
        $tasks = LibraryTask::whereIn('task_id', array_column($items, 'task_id'))->get()->keyBy('task_id');
        $total = BigDecimal::zero()->toScale(2);
        $lines = [];
        foreach ($items as $index => $item) {
            $task = $tasks->get($item['task_id']);
            if (! $task) {
                throw \Illuminate\Validation\ValidationException::withMessages(["lines.{$index}.task_id" => 'The selected Library task no longer exists.']);
            }
            $quote = $task->pricing_mode === 'quote_by_scope';
            $rate = $quote ? ($item['rate'] ?? null) : $task->bidfreq_standard_rate;
            if ($rate === null || (! $quote && isset($item['rate']))) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "lines.{$index}.rate" => $quote ? 'Enter a rate for the quote-by-scope task.' : 'Fixed-rate tasks use the Library rate.',
                ]);
            }

            $lineTotal = self::lineTotal($item['quantity'], $rate);
            $total = $total->plus($lineTotal);
            $lines[] = [
                'task_id' => $task->task_id, 'library_version_id' => $task->library_version_id,
                'name' => $task->task_name, 'unit' => $task->unit, 'quantity' => $item['quantity'], 'rate' => $rate,
                'line_total' => (string) $lineTotal, 'priced_on' => now()->toDateString(), 'source' => 'manual',
                'flags' => in_array($task->task_id, LibraryTask::AMBIGUOUS, true) ? ['Confirm unit and quantity'] : [],
                'evidence' => [],
            ];
        }

        return [
            'draft' => ['analysis' => $analysis, 'total' => (string) $total, 'over_nte' => $repair->nte !== null && $total->isGreaterThan($repair->nte)],
            'lines' => $lines,
        ];
    }

    private function strings(mixed $values): bool
    {
        return is_array($values) && count($values) <= 300 && collect($values)->every(
            fn ($value) => is_string($value) && trim($value) !== '' && mb_strlen($value) <= 2000
        );
    }

    private function quantity(mixed $value): ?string
    {
        if (is_int($value)) {
            $value = (string) $value;
        }

        return is_string($value) && preg_match('/^\d{1,8}(?:\.\d{1,6})?$/D', $value) && BigDecimal::of($value)->isPositive()
            ? $value : null;
    }
}
