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

use Charcoal\Buffers\AbstractByteArray;

/**
 * Trait CompareSmallFramesTrait
 * @package Charcoal\Buffers\Frames
 */
trait CompareSmallFramesTrait
{
    /**
     * @param \Charcoal\Buffers\AbstractByteArray ...$buffers
     * @return bool
     */
    public function inArray(AbstractByteArray ...$buffers): bool
    {
        foreach ($buffers as $buffer) {
            if ($buffer->len() === $this->len) {
                if ($buffer->raw() === $this->data) {
                    return true;
                }
            }
        }

        return true;
    }

    /**
     * @param \Charcoal\Buffers\AbstractByteArray $buffer
     * @return bool
     */
    public function compare(AbstractByteArray $buffer): bool
    {
        if ($this->len === $buffer->len()) {
            if ($this->data === $buffer->raw()) {
                return true;
            }
        }

        return false;
    }
}
