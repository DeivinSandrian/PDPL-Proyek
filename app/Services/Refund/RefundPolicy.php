<?php

namespace App\Services\Refund;

use App\Models\Booking;
use Carbon\CarbonInterface;

/**
 * Pure refund-amount calculator driven by config/refund.php.
 *
 * Tiers are read as an array shaped like:
 *
 *     [
 *         ['min_hours' => 72, 'percentage' => 0.90],
 *         ['min_hours' => 24, 'percentage' => 0.50],
 *         ['min_hours' => 0,  'percentage' => 0.00],
 *     ]
 *
 * Given a Booking and the current moment, the policy:
 *
 *   1. Computes the signed hours remaining until the schedule's departure.
 *   2. Sorts the configured tiers descending by `min_hours`.
 *   3. Picks the first tier whose `min_hours` threshold is satisfied
 *      (i.e., the largest `min_hours` <= hours-until-departure).
 *   4. Returns `total_amount * percentage`, rounded to 2 decimal places.
 *
 * The class is a pure function of its inputs and the current configuration:
 * it performs no database writes and has no other observable side effects.
 *
 * Implements R12.6.
 */
class RefundPolicy
{
    /**
     * Compute the refund amount for the given booking at the given moment.
     *
     * @return float Refund amount in IDR rounded to 2 decimal places.
     */
    public function compute(Booking $booking, CarbonInterface $now): float
    {
        // Ensure the related schedule is available without re-querying when
        // it has already been eager loaded by the caller.
        $booking->loadMissing('schedule');

        $schedule = $booking->schedule;

        if ($schedule === null || $schedule->departure_time === null) {
            return 0.0;
        }

        // Signed hours from $now to departure_time. Positive when departure
        // is in the future, negative when it is already in the past.
        $hoursUntilDeparture = (float) $now->diffInHours(
            $schedule->departure_time,
            false
        );

        $tiers = (array) config('refund.tiers', []);

        // Sort descending by min_hours so the largest threshold satisfied
        // is selected first.
        usort(
            $tiers,
            static fn (array $a, array $b): int => ($b['min_hours'] ?? 0)
                <=> ($a['min_hours'] ?? 0)
        );

        $percentage = 0.0;
        foreach ($tiers as $tier) {
            $minHours = (float) ($tier['min_hours'] ?? 0);
            if ($hoursUntilDeparture >= $minHours) {
                $percentage = (float) ($tier['percentage'] ?? 0.0);
                break;
            }
        }

        $totalAmount = (float) $booking->total_amount;

        return round($totalAmount * $percentage, 2);
    }
}
