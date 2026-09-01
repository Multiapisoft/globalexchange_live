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

use Charcoal\Buffers\ByteOrder;

/**
 * Class AbstractEndianness
 * @package Charcoal\Buffers\ByteOrder
 */
abstract class AbstractEndianness
{
    /**
     * Constructor is disabled, no instances should be necessary
     */
    private function __construct()
    {
    }

    /**
     * Packs an 8-bit integer
     * @param int $n
     * @return string
     */
    public static function PackUInt8(int $n): string
    {
        ByteOrder::CheckUInt32($n, 1); // Verify that argument int can be packed in a single byte
        return chr($n);
    }

    /**
     * Unpacks an 8-bit integer
     * @param string $bn
     * @return int
     */
    public static function UnpackUInt8(string $bn): int
    {
        return ord($bn);
    }
}