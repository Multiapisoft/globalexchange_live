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

use Charcoal\Buffers\AbstractFixedLenBuffer;

/**
 * Class Bytes16
 * - Use this frame for buffers of precisely 16 bytes.
 * - If value is smaller than 16 bytes, LengthException will be thrown.
 * @package Charcoal\Buffers\Frames
 */
class Bytes16 extends AbstractFixedLenBuffer
{
    public const SIZE = 16; // Fixed frame-size of 16 bytes
    protected const PAD_TO_LENGTH = null; // No padding, constructor argument MUST be precisely 16 bytes

    use CompareSmallFramesTrait;
}
