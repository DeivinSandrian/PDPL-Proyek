<?php

namespace App\Services;

use App\Models\Booking;
use RuntimeException;

/**
 * Generates booking codes that match /^BK[A-Z0-9]{8}$/ (total length 10).
 *
 * The generator uses a cryptographically secure source (random_int) to pick
 * characters from the alphanumeric alphabet [A-Z0-9] and retries on a
 * UNIQUE collision against bookings.booking_code up to MAX_ATTEMPTS times.
 *
 * Implements requirements R7.1 (format) and R7.2 (uniqueness).
 */
class BookingCodeGenerator
{
    /**
     * Fixed prefix required by R7.1.
     */
    private const PREFIX = 'BK';

    /**
     * Number of alphanumeric characters appended after the prefix.
     */
    private const SUFFIX_LENGTH = 8;

    /**
     * Alphabet used for the suffix: uppercase A-Z plus digits 0-9.
     */
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * Maximum number of attempts before giving up on collision retries.
     * With a 36^8 ≈ 2.8 * 10^12 keyspace, exhausting 8 attempts is a strong
     * signal of a deeper problem rather than ordinary bad luck.
     */
    private const MAX_ATTEMPTS = 8;

    /**
     * Generate a unique booking code.
     *
     * @throws RuntimeException When all retry attempts collide.
     */
    public function generate(): string
    {
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $code = $this->buildCandidate();

            if (! Booking::where('booking_code', $code)->exists()) {
                return $code;
            }
        }

        throw new RuntimeException(
            'Booking code generator exhausted retries after '
                . self::MAX_ATTEMPTS . ' attempts'
        );
    }

    /**
     * Build a single candidate code without checking for uniqueness.
     */
    private function buildCandidate(): string
    {
        $alphabetMaxIndex = strlen(self::ALPHABET) - 1;
        $suffix = '';

        for ($i = 0; $i < self::SUFFIX_LENGTH; $i++) {
            $suffix .= self::ALPHABET[random_int(0, $alphabetMaxIndex)];
        }

        return self::PREFIX . $suffix;
    }
}
