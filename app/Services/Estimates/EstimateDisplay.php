<?php

namespace App\Services\Estimates;

use App\Models\RepairRequest;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class EstimateDisplay
{
    public static function money(string|int|null $value): string
    {
        return $value === null ? '—' : (string) BigDecimal::of($value)->toScale(2, RoundingMode::HalfUp);
    }

    public static function currency(string|int|null $value): string
    {
        return $value === null ? '—' : '$'.self::money($value);
    }

    public static function hours(string|int|null $value): string
    {
        return $value === null ? '—' : self::quantity(self::money($value));
    }

    public static function reviewFlag(string $flag): array
    {
        return match ($flag) {
            'Unmatched Library task', 'Unmatched work' => ['There is no matching Library task for this line; select one or exclude the line.', 'task_id'],
            'Unit does not match the Library', 'Confirm unit and quantity' => ['Confirm the Library unit and check the quantity before saving.', 'confirmed'],
            'Invalid quantity basis', 'Quantity could not be determined' => ['The quantity needs verification; check the evidence and enter the correct quantity.', 'quantity'],
            'Quote by scope: Admin rate required' => ['This task is priced by scope; enter the Admin quote rate.', 'rate'],
            'Evidence is missing or invalid' => ['This line needs support from the submitted evidence; review it before saving.', 'evidence'],
            default => [rtrim($flag, '.').'; review the Library task before saving.', 'task_id'],
        };
    }

    public static function quantity(string|int|null $value): string
    {
        if ($value === null) {
            return '—';
        }

        return str_contains((string) $value, '.') ? rtrim(rtrim((string) $value, '0'), '.') : (string) $value;
    }

    public static function measurementWarnings(RepairRequest $repair): array
    {
        $warnings = [];
        if (BigDecimal::of($repair->home_sqft)->isLessThan('100')) {
            $warnings[] = 'The reported home area is unusually small. Confirm the total home square footage.';
        }

        foreach ($repair->dimensions as $area) {
            if (! isset($area['length'], $area['width'], $area['area'])) {
                continue;
            }
            $calculated = BigDecimal::of($area['length'])->multipliedBy($area['width']);
            if ($calculated->minus($area['area'])->abs()->isGreaterThanOrEqualTo('0.01')) {
                $warnings[] = $area['label'].': length × width does not match the reported area. Confirm the measurements.';
            }
        }

        return $warnings;
    }
}
