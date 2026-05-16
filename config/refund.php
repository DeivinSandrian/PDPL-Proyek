<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Refund Tiers
    |--------------------------------------------------------------------------
    |
    | Tiered refund percentages keyed by the minimum number of hours that must
    | remain between "now" and the schedule's departure_time for the tier to
    | apply. The RefundPolicy iterates these tiers from largest to smallest
    | min_hours and picks the first tier whose threshold is satisfied. A
    | percentage is expressed as a fraction in [0.0, 1.0] (e.g. 0.90 = 90%).
    |
    | Default policy:
    |   - 72h or more before departure   => 90% refund
    |   - 24h up to 72h before departure => 50% refund
    |   - less than 24h before departure =>  0% refund
    |
    | Implements R12.6.
    |
    */

    'tiers' => [
        ['min_hours' => 72, 'percentage' => 0.90],
        ['min_hours' => 24, 'percentage' => 0.50],
        ['min_hours' => 0,  'percentage' => 0.0],
    ],

];
