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

/**
 * Class FramesTest
 */
class FramesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Basic check
     * @return void
     */
    public function testFrameFixLength(): void
    {
        $hash = hash("sha256", "furqansiddiqui", true);
        $frame = new \Charcoal\Buffers\Frames\Bytes32($hash);
        $this->assertEquals(32, $frame->len());
    }

    /**
     * Attempt to give a smaller length value to Bytes20P frame
     * @return void
     */
    public function testFramePaddedLength(): void
    {
        $frame = new \Charcoal\Buffers\Frames\Bytes20P("furqansiddiqui");
        $this->assertEquals(20, $frame->len());
        $this->assertEquals("\0\0\0\0\0\0furqansiddiqui", $frame->raw());
    }

    /**
     * Attempt to give 32 bytes value to a 20 byte frame
     * @return void
     */
    public function testFrameOverflow(): void
    {
        $this->expectException('LengthException');
        new \Charcoal\Buffers\Frames\Bytes20(hash("sha256", "furqansiddiqui", true));
    }

    /**
     * Attempt to give 32 bytes value to a 20 byte padded frame
     * @return void
     */
    public function testPaddedFrameOverflow(): void
    {
        $this->expectException('LengthException');
        new \Charcoal\Buffers\Frames\Bytes20P(hash("sha256", "furqansiddiqui", true));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testFromRandom(): void
    {
        $b16 = \Charcoal\Buffers\Frames\Bytes16::fromRandomBytes();
        $this->assertEquals(16, $b16->len());
        $b20 = \Charcoal\Buffers\Frames\Bytes20::fromRandomBytes();
        $this->assertEquals(20, $b20->len());
        $b32 = \Charcoal\Buffers\Frames\Bytes32::fromRandomBytes();
        $this->assertEquals(32, $b32->len());
        $b32p = \Charcoal\Buffers\Frames\Bytes32P::fromRandomBytes();
        $this->assertEquals(32, $b32p->len());
    }
}