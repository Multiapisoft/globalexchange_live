<?php
/*
 * This file is a part of "charcoal-dev/buffers" package.
 * https://github.com/charcoal-dev/buffers
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/buffers/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Buffers\ByteOrder;

use Charcoal\Adapters\GMP\BigInteger;
use Charcoal\Buffers\AbstractByteArray;
use Charcoal\Buffers\ByteOrder;

/**
 * Class LittleEndian
 * @package Charcoal\Buffers\ByteOrder
 */
class LittleEndian extends AbstractEndianness
{
    /**
     * @param int $n
     * @return string
     */
    public static function PackUInt16(int $n): string
    {
        ByteOrder::CheckUInt32($n, 2);
        return pack("v", $n);
    }

    /**
     * @param string $bn
     * @return int
     */
    public static function UnpackUInt16(string $bn): int
    {
        if (strlen($bn) !== 2) {
            throw new \OverflowException('Input exceeds 2 bytes');
        }

        return unpack("v", $bn)[1];
    }

    /**
     * @param int $n
     * @return string
     */
    public static function PackUInt32(int $n): string
    {
        ByteOrder::CheckUInt32($n, 4);
        return pack("V", $n);
    }

    /**
     * @param string $bn
     * @return int
     */
    public static function UnpackUInt32(string $bn): int
    {
        if (strlen($bn) !== 4) {
            throw new \OverflowException('Input exceeds 4 bytes');
        }

        return unpack("V", $bn)[1];
    }

    /**
     * @param int|string|\GMP|\Charcoal\Buffers\AbstractByteArray|\Charcoal\Adapters\GMP\BigInteger $n
     * @return string
     */
    public static function PackUInt64(int|string|\GMP|AbstractByteArray|BigInteger $n): string
    {
        $n = ByteOrder::CheckUInt64($n);
        $packed = str_pad(hex2bin($n->toBase16()), 8, "\0", STR_PAD_LEFT);
        if (ByteOrder::gmpEndianness() !== ByteOrder::LITTLE_ENDIAN) {
            $packed = ByteOrder::SwapEndianness($packed, false);
        }

        return $packed;
    }

    /**
     * @param string $bn
     * @param bool $checkEndianness
     * @return \Charcoal\Adapters\GMP\BigInteger
     */
    public static function UnpackUInt64(string $bn, bool $checkEndianness = true): BigInteger
    {
        if (strlen($bn) !== 8) {
            throw new \OverflowException('Input exceeds 8 bytes');
        }

        if ($checkEndianness && ByteOrder::gmpEndianness() !== ByteOrder::LITTLE_ENDIAN) {
            $bn = ByteOrder::SwapEndianness($bn, false);
        }

        return new BigInteger(gmp_init(bin2hex($bn), 16));
    }

    /**
     * @param int|string|\GMP $int
     * @return string
     */
    public static function GMP_Pack(int|string|\GMP $int): string
    {
        return gmp_export($int instanceof \GMP ? $int : gmp_init($int, 10), 1, GMP_LSW_FIRST | GMP_NATIVE_ENDIAN);
    }

    /**
     * @param string $bn
     * @return \GMP
     */
    public static function GMP_Unpack(string $bn): \GMP
    {
        return gmp_import($bn, 1, GMP_LSW_FIRST | GMP_NATIVE_ENDIAN);
    }
}

