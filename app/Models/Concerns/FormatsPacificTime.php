<?php

namespace App\Models\Concerns;

use Carbon\Carbon;

trait FormatsPacificTime
{
    protected function formatPacificTime($datetime): string
    {
        if (!$datetime) {
            return '';
        }

        return Carbon::parse($datetime)
            ->timezone('America/Los_Angeles')
            ->format('Y-m-d H:i:s T');
    }

    protected function formatPacificDate($datetime): string
    {
        if (!$datetime) {
            return '';
        }

        return Carbon::parse($datetime)
            ->timezone('America/Los_Angeles')
            ->format('M j, Y');
    }
}
