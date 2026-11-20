<?php

namespace App\Services\Library;

use App\Models\LibraryTask;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use JsonException;
use RuntimeException;

class LibraryFormat
{
    public const FIELDS = [
        'task_id', 'task_name', 'unit', 'evergreen_low', 'evergreen_typical', 'evergreen_high',
        'times_seen_scopes', 'bidfreq_standard_rate', 'standard_hours', 'hours_basis',
        'labor_cost_market_avg', 'source', 'effective_date', 'version', 'last_updated',
    ];

    public const NUMBERS = [
        'evergreen_low', 'evergreen_typical', 'evergreen_high', 'times_seen_scopes',
        'bidfreq_standard_rate', 'standard_hours', 'labor_cost_market_avg',
    ];

    public function read(string $path, string $extension): array
    {
        $size = filesize($path);
        if ($size === false || $size > 2_097_152) {
            throw new RuntimeException('Choose a file no larger than 2 MB.');
        }
        $contents = file_get_contents($path);
        if ($contents === false || ! mb_check_encoding($contents, 'UTF-8') || str_contains($contents, "\0")) {
            throw new RuntimeException('Choose a valid UTF-8 file.');
        }
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);
        $records = match ($extension) {
            'csv' => $this->readCsv($contents),
            'json' => $this->readJson($contents),
            default => throw new RuntimeException('Choose a CSV or JSON file.'),
        };

        if ($records === [] || count($records) > 2000) {
            throw new RuntimeException('The file must contain between 1 and 2,000 task rows.');
        }

        $rows = $errors = $seen = [];
        foreach ($records as $number => $row) {
            if (! is_array($row) || ! $this->hasFields(array_keys($row))) {
                $errors[] = "Row {$number}: expected all 15 Library fields.";
                continue;
            }

            $id = $row['task_id'];
            if (is_string($id) && isset($seen[$id])) {
                $errors[] = "Row {$number}: duplicate task ID {$id}.";
                continue;
            }
            if (is_string($id)) {
                $seen[$id] = true;
            }

            try {
                $normalized = $this->normalize($row);
                $rows[$normalized['task_id']] = $normalized;
            } catch (ValidationException $e) {
                foreach ($e->validator->errors()->all() as $message) {
                    $errors[] = "Row {$number}: {$message}";
                }
            }
        }

        return [$rows, $errors];
    }

    public function normalize(array $row): array
    {
        foreach (self::NUMBERS as $field) {
            if (isset($row[$field]) && (is_int($row[$field]) || is_float($row[$field]))) {
                $row[$field] = (string) $row[$field];
            }
        }

        $decimal = 'regex:/^\d{1,10}(?:\.\d{1,6})?$/D';
        $rate = $row['bidfreq_standard_rate'] ?? null;
        $quote = is_string($rate) && preg_match('/^Quote by scope(?: \(reference \$((?:\d{1,3}(?:,\d{3})+|\d{1,10})(?:\.\d{1,6})?)\))?$/D', $rate, $parts);
        $data = Validator::make($row, [
            'task_id' => ['required', 'string', 'max:32', 'regex:/^[A-Z][A-Z0-9]*(?:-[A-Z0-9]+)*$/D'],
            'task_name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:100'],
            'evergreen_low' => ['required', 'string', $decimal],
            'evergreen_typical' => ['required', 'string', $decimal],
            'evergreen_high' => ['required', 'string', $decimal],
            'times_seen_scopes' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'bidfreq_standard_rate' => $quote ? ['required', 'string', 'max:255'] : ['required', 'string', $decimal],
            'standard_hours' => ['required', 'string', $decimal],
            'hours_basis' => ['required', 'string', 'max:255'],
            'labor_cost_market_avg' => ['required', 'string', $decimal],
            'source' => ['required', 'string', 'max:2000'],
            'effective_date' => ['required', 'date_format:Y-m-d'],
            'version' => ['required', 'string', 'max:50'],
            'last_updated' => ['required', 'date_format:Y-m-d'],
        ], ['regex' => 'The :attribute format is invalid. Use a nonnegative decimal with up to six decimal places for numeric fields.'])->validate();

        foreach (['task_name', 'unit', 'source', 'hours_basis', 'version'] as $field) {
            if (preg_match('/^\s*[=+\-@]|^[\t\r\n]/u', $data[$field])) {
                throw ValidationException::withMessages([$field => 'This value starts with a spreadsheet formula marker.']);
            }
        }

        foreach (array_diff(self::NUMBERS, ['times_seen_scopes', 'bidfreq_standard_rate']) as $field) {
            $data[$field] = $this->decimal($data[$field]);
        }
        if (BigDecimal::of($data['evergreen_low'])->compareTo($data['evergreen_typical']) > 0
            || BigDecimal::of($data['evergreen_typical'])->compareTo($data['evergreen_high']) > 0) {
            throw ValidationException::withMessages(['evergreen_typical' => 'The Evergreen range must be ordered low, typical, high.']);
        }

        $reference = $quote && isset($parts[1]) ? str_replace(',', '', $parts[1]) : null;
        if ($reference !== null && ! preg_match('/^\d{1,10}(?:\.\d{1,6})?$/D', $reference)) {
            throw ValidationException::withMessages(['bidfreq_standard_rate' => 'The reference amount exceeds the supported precision.']);
        }

        $data['times_seen_scopes'] = (int) $data['times_seen_scopes'];
        $data['pricing_mode'] = $quote ? 'quote_by_scope' : 'fixed';
        $data['rate_label'] = $quote ? $rate : null;
        $data['reference_rate'] = $reference === null ? null : $this->decimal($reference);
        $data['bidfreq_standard_rate'] = $quote ? null : $this->decimal($rate);

        return $data;
    }

    public function row(LibraryTask $task): array
    {
        $row = [];
        foreach (self::FIELDS as $field) {
            $value = $field === 'bidfreq_standard_rate' ? ($task->rate_label ?? $task->$field) : $task->$field;
            $row[$field] = in_array($field, self::NUMBERS, true) && is_string($value) && is_numeric($value)
                ? $this->decimal($value) : $value;
        }
        return $row;
    }

    public function normalizedRow(array $row): array
    {
        $row['bidfreq_standard_rate'] = $row['rate_label'] ?? $row['bidfreq_standard_rate'];

        return array_replace(array_fill_keys(self::FIELDS, null), array_intersect_key($row, array_flip(self::FIELDS)));
    }

    public function jsonRow(array $row): string
    {
        $fields = [];
        foreach ($row as $field => $value) {
            $literal = in_array($field, self::NUMBERS, true) && is_numeric($value)
                ? (string) $value : json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $fields[] = json_encode($field, JSON_THROW_ON_ERROR).':'.$literal;
        }
        return '{'.implode(',', $fields).'}';
    }

    private function decimal(string $decimal): string
    {
        [$whole, $fraction] = array_pad(explode('.', $decimal, 2), 2, '');
        $whole = ltrim($whole, '0') ?: '0';
        $fraction = rtrim($fraction, '0');

        return $whole.($fraction === '' ? '' : '.'.$fraction);
    }

    private function readJson(string $contents): array
    {
        try {
            // Check the original grammar, then preserve number tokens as exact decimal text.
            $original = json_decode($contents, false, 32, JSON_THROW_ON_ERROR);
            $decimalJson = preg_replace_callback(
                '~"(?:[^"\\\\]|\\\\.)*"(*SKIP)(*F)|-?(?:0|[1-9]\d*)(?:\.\d+)?(?:[eE][+-]?\d+)?~s',
                fn (array $number) => '"'.$number[0].'"',
                $contents,
            );
            if ($decimalJson === null) {
                throw new RuntimeException('The JSON numbers could not be read.');
            }
            $data = json_decode($decimalJson, false, 32, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new RuntimeException('The JSON file could not be parsed.');
        }
        if (! is_array($data)) {
            throw new RuntimeException('The JSON file must contain an array of task objects.');
        }

        $result = [];
        foreach ($original as $i => $row) {
            if (! $row instanceof \stdClass) {
                $result[$i + 1] = null;
                continue;
            }

            $record = get_object_vars($row);
            foreach (self::NUMBERS as $field) {
                if (isset($record[$field]) && (is_int($record[$field]) || is_float($record[$field]))) {
                    $record[$field] = $data[$i]->$field;
                }
            }
            $result[$i + 1] = $record;
        }
        return $result;
    }

    private function readCsv(string $contents): array
    {
        $stream = fopen('php://temp', 'w+b');
        try {
            fwrite($stream, $contents);
            rewind($stream);
            $header = fgetcsv($stream, escape: '');
            if (! is_array($header) || ! $this->hasFields($header)) {
                throw new RuntimeException('The CSV header must contain each of the 15 Library fields once.');
            }

            $records = [];
            $number = 1;
            while (($values = fgetcsv($stream, escape: '')) !== false) {
                $number++;
                if ($values !== [null]) {
                    $records[$number] = count($values) === count($header) ? array_combine($header, $values) : null;
                }
            }

            return $records;
        } finally {
            fclose($stream);
        }
    }

    private function hasFields(array $fields): bool
    {
        return count($fields) === count(self::FIELDS) && array_diff(self::FIELDS, $fields) === [];
    }
}
