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
 * Class BufferManipulationTest
 */
class BufferManipulationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test when instance of AbstractByteArray (any buffer) is treated as string
     * @return void
     */
    public function testAsString(): void
    {
        $buffer = \Charcoal\Buffers\Buffer::fromBase16("63686172636f616c");
        $this->assertEquals(8, strlen((string)$buffer));
        $this->assertEquals("charcoal", strval($buffer));
    }

    /**
     * @return void
     */
    public function testBufferFromMultipleCodes(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer(); // Blank buffer
        $buffer->append(\Charcoal\Buffers\Buffer::fromBase16("63686172"));
        $buffer->append(\Charcoal\Buffers\Buffer::fromByteArray([0x63, 0x6f, 0x61, 0x6c]));
        $this->assertEquals("charcoal", $buffer->raw());
    }

    /**
     * @return void
     */
    public function testPopMethod(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer("charcoal");
        $this->assertEquals("ch", $buffer->pop(2, changeBuffer: false)); // Pop 2 bytes, don't change buffer
        $this->assertEquals("charcoal", $buffer->raw()); // Buffer was not altered
        $this->assertEquals("char", $buffer->pop(4, changeBuffer: true)); // Pop first 4 bytes, CHANGE the buffer internally
        $this->assertEquals("coal", $buffer->raw()); // Buffer was updated, it is now just "coal"
        $this->assertEquals("al", $buffer->pop(-2, changeBuffer: false)); // Get last 2 bytes, don't change the buffer
        $this->assertEquals("coal", $buffer->raw()); // Buffer still intact
        $this->assertEquals("oal", $buffer->pop(-3, changeBuffer: true)); // Get last 3 bytes, CHANGE the buffer
        $this->assertEquals("c", $buffer->raw()); // Buffer was changed, now its only "c"
    }

    /**
     * @return void
     */
    public function testCopyAndEqualMethods(): void
    {
        $buffer1 = new \Charcoal\Buffers\Buffer("charcoal");

        $this->assertEquals("charcoal", $buffer1->copy()->raw(), "Simple Copy");
        $this->assertEquals("char", $buffer1->copy(0, 4)->raw(), "Copy initial 4 bytes as new Buffer");
        $this->assertEquals("charcoal", $buffer1->raw(), "Original buffer is intact");
        $this->assertEquals("harc", $buffer1->copy(1, 4)->raw(), "Get 4 bytes starting after first byte");
        $this->assertEquals("coal", $buffer1->copy(-4)->raw(), "Copy last 4 bytes as new Buffer");
        $this->assertEquals("coa", $buffer1->copy(-4, 3)->raw(), "Copy 3 bytes from set of last 4 bytes");
        $this->assertEquals("arcoal", $buffer1->copy(2)->raw(), "Copy all bytes as new Buffer except first 2");

        $coal = new \Charcoal\Buffers\Buffer("coal");
        $this->assertTrue($buffer1->copy(-4)->equals($coal));
        $this->assertFalse($buffer1->copy(-4)->equals(new \Charcoal\Buffers\Buffer("cola")));
    }

    /**
     * @return void
     */
    public function testSwitchEndiannessMethod(): void
    {
        $buffer = \Charcoal\Buffers\Buffer::fromBase16("a1b2c3");
        $switched = $buffer->switchEndianness();
        $this->assertEquals("c3b2a1", $switched->toBase16());
    }

    /**
     * @return void
     */
    public function testApplyFn(): void
    {
        $buffer = new Charcoal\Buffers\Buffer(" charcoal\t\0");
        $manipulated = $buffer->applyFn(function (string $bytes) {
            return strtolower(trim($bytes)) . ".dev";
        });

        $this->assertEquals("charcoal.dev", $manipulated->raw());
    }
}