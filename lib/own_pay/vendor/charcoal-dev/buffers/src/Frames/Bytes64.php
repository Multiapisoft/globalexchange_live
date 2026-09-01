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

namespace Charcoal\Buffers\Frames;

use Charcoal\Buffers\AbstractFixedLenBuffer;

/**
 * Class Bytes64
 * - Use this frame for buffers of precisely 64 bytes.
 * - If value is smaller than 64 bytes, LengthException will be thrown.
 * @package Charcoal\Buffers\Frames
 */
class Bytes64 extends AbstractFixedLenBuffer
{
    public const SIZE = 64; // Fixed frame-size of 64 bytes
    protected const PAD_TO_LENGTH = null; // No padding, constructor argument MUST be precisely 64 bytes

    use CompareSmallFramesTrait;
}
