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

namespace Charcoal\Buffers;

/**
 * Class AbstractFixedLenBuffer
 * @package Charcoal\Buffers
 */
abstract class AbstractFixedLenBuffer extends AbstractByteArray
{
    /** @var null|int Fixed size of buffer in bytes */
    public const SIZE = null;
    /** @var null|int Pads data if smaller than expected len; set with either STR_PAD_* const */
    protected const PAD_TO_LENGTH = null;

    /**
     * @param string $bytes
     * @return void
     */
    protected function setBuffer(string $bytes): void
    {
        if (is_int(static::PAD_TO_LENGTH)) {
            $bytes = str_pad($bytes, static::SIZE, "\0", static::PAD_TO_LENGTH);
        }

        if (strlen($bytes) !== static::SIZE) {
            throw new \LengthException(sprintf(
                '%s buffer expects fixed length of %d bytes; given %d bytes',
                (new \ReflectionClass($this))->getShortName(),
                static::SIZE,
                strlen($bytes)
            ));
        }

        parent::setBuffer($bytes);
    }

    /**
     * @return static
     */
    public static function fromRandomBytes(): static
    {
        try {
            return new static(random_bytes(static::SIZE));
        } catch (\Exception) {
            throw new \RuntimeException(
                sprintf('Failed to source %d bytes from cryptographically-secure PRNG method', static::SIZE)
            );
        }
    }
}
