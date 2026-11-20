<?php

namespace App\Console\Commands;

use App\Models\LibraryTask;
use App\Models\User;
use App\Services\Library\PricingLibrary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ImportPricingLibrary extends Command
{
    protected $signature = 'bidfreq:library-import {file} {--email= : Verified staff account for the audit log} {--apply : Apply after preview passes}';
    protected $description = 'Validate and preview a Pricing Library file with optional transactional apply';

    public function handle(PricingLibrary $library): int
    {
        $user = User::where('email', strtolower((string) $this->option('email')))->first();
        if (! $user || $user->status !== 'active' || ! $user->hasVerifiedEmail() || ! Gate::forUser($user)->allows('manage', LibraryTask::class)) {
            $this->error('Choose an active, verified Admin or Super Admin with --email.');

            return self::FAILURE;
        }

        $path = (string) $this->argument('file');
        if (! is_file($path) || ! is_readable($path)) {
            $this->error('The Library file could not be read.');

            return self::FAILURE;
        }

        try {
            $preview = $library->preview($path, basename($path), $user);
            [$counts] = $library->changes($preview->rows);
            $this->table(array_keys($counts), [array_values($counts)]);
            if ($preview->errors !== []) {
                foreach ($preview->errors as $error) {
                    $this->error($error);
                }
                return self::FAILURE;
            }
            if ($this->option('apply')) {
                $this->info('Applied Library version #'.$library->apply($preview->id, $user)->id.'.');
            }
        } catch (ValidationException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
