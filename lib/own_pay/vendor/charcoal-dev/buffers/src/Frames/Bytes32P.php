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

namespace Charcoal\Buffers\Frames;

/**
 * Class Bytes32P
 *  - Use this frame for buffers of length 32 bytes or fewer.
 *  - If value is smaller than 32 bytes, it will be padded with NULL bytes to its left.
 *  - This class extends Bytes32 so using this in place of Bytes32 type hinting is compatible.
 * @package Charcoal\Buffers\Frames
 */
class Bytes32P extends Bytes32
{
    public const PAD_TO_LENGTH = STR_PAD_LEFT;
}

