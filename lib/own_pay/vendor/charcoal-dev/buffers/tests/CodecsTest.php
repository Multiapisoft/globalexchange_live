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
 * Class CodecsTest
 */
class CodecsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testSerialize(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer("charcoal");
        $ser1 = serialize($buffer);
        $ser2 = serialize($buffer->hash()->sha1());

        /** @var \Charcoal\Buffers\AbstractByteArray $restored1 */
        $restored1 = unserialize($ser1);
        /** @var \Charcoal\Buffers\AbstractByteArray $restored2 */
        $restored2 = unserialize($ser2);

        $this->assertInstanceOf(\Charcoal\Buffers\Buffer::class, $restored1);
        $this->assertEquals(8, $restored1->len());
        $this->assertEquals("charcoal", $restored1->raw());

        $this->assertInstanceOf(\Charcoal\Buffers\Frames\Bytes20::class, $restored2);
        $this->assertEquals(20, $restored2->len());
    }

    /**
     * @return void
     */
    public function testBase16(): void
    {
        $raw = hash("sha256", "furqansiddiqui", true);
        $hex = bin2hex($raw);

        $frame1 = \Charcoal\Buffers\Frames\Bytes32::fromBase16("0x" . $hex);
        $frame2 = \Charcoal\Buffers\Frames\Bytes32::fromBase16($hex);

        $this->assertTrue($frame1->equals($frame2), "Testing that 0x prefix is discarded");
        $this->assertEquals($frame1->toBase16(), $hex, "Compare hex strings");
        $this->assertEquals($frame1->raw(), $raw, "Compare raw strings");
    }

    /**
     * @return void
     */
    public function testByteArray(): void
    {
        $buffer = \Charcoal\Buffers\Buffer::fromBase16("73616d706c65");
        $this->assertEquals([115, 97, 109, 112, 0x6c, 0x65], $buffer->toByteArray());
    }

    /**
     * @return void
     */
    public function testByteArrayWithUTF8(): void
    {
        $bA = [217, 129, 216, 177, 217, 130, 216, 167, 217, 134];
        $buffer2 = \Charcoal\Buffers\Buffer::fromByteArray($bA);
        $this->assertNotEquals("فرقا", $buffer2->raw());
        $this->assertEquals("فرقان", $buffer2->raw());

        $buffer3 = new \Charcoal\Buffers\Buffer("فرقان");
        $this->assertEquals($bA, $buffer3->toByteArray());
    }

    /**
     * @return void
     */
    public function testBase64(): void
    {
        $string = "YjY0AHRlc3Q=";
        $buffer = \Charcoal\Buffers\Buffer::fromBase64($string);
        $this->assertNotEquals("b64 test", $buffer->raw());
        $this->assertEquals("b64\0test", $buffer->raw());
        $this->assertEquals($string, $buffer->toBase64());
    }
}