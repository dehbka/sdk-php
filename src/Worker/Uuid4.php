<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Client\Worker;

/**
 * A static helper class that implements the logic for generating UUID 4 based
 * on RFC 4122.
 *
 * @internal Uuid4 is an internal library class, please do not use it in your code.
 * @psalm-internal Temporal\Client\Worker
 */
final class Uuid4
{
    /**
     * Returns an RFC 4122 variant Uuid, created from the provided bytes and
     * version.
     *
     * @see http://tools.ietf.org/html/rfc4122
     * @return string
     * @throws \Exception
     */
    public static function create(): string
    {
        $uuid = \bin2hex(self::bytes());

        return \vsprintf('%s-%s-%s-%s-%s', [
            \substr($uuid, 0, 8),
            \substr($uuid, 8, 4),
            \substr($uuid, 12, 4),
            \substr($uuid, 16, 4),
            \substr($uuid, 20, 12),
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private static function bytes(): string
    {
        $bytes = \random_bytes(16);

        $timeHi = (int)\unpack('n*', \substr($bytes, 6, 2))[1];
        $timeHiAndVersion = \pack('n*', self::version($timeHi, 4));

        $clockSeqHi = (int)\unpack('n*', \substr($bytes, 8, 2))[1];
        $clockSeqHiAndReserved = \pack('n*', self::variant($clockSeqHi));

        $bytes = \substr_replace($bytes, $timeHiAndVersion, 6, 2);

        return \substr_replace($bytes, $clockSeqHiAndReserved, 8, 2);
    }

    /**
     * Applies the RFC 4122 version number to the 16-bit `time_hi_and_version`
     * field.
     *
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
     *
     * @psalm-pure
     * @param int $timeHi
     * @param int $version
     * @return int
     */
    private static function version(int $timeHi, int $version): int
    {
        return $timeHi & 0x0fff | ($version << 12);
    }

    /**
     * Applies the RFC 4122 variant field to the 16-bit clock sequence.
     *
     * @see http://tools.ietf.org/html/rfc4122#section-4.1.1
     *
     * @psalm-pure
     * @param int $clockSeq
     * @return int
     */
    private static function variant(int $clockSeq): int
    {
        return $clockSeq & 0x3fff | 0x8000;
    }
}
