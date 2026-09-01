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
 * Class VarLengthBuffersTest
 */
class VarLengthBuffersTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testNewBufferIsWritable(): void
    {
        $this->assertTrue((new \Charcoal\Buffers\Buffer(""))->isWritable());
    }

    /**
     * @return void
     */
    public function testReadOnly(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer("charcoal");
        $buffer->readOnly();

        $this->expectException('BadMethodCallException');
        $buffer->append(".dev");
    }

    /**
     * @return void
     */
    public function testWritable(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer("charcoal");
        $buffer->writable();
        $buffer->append(".dev");
        $this->assertEquals("charcoal.dev", $buffer->raw());
    }

    /**
     * @return void
     */
    public function testAppendPrepend(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer("coal");
        $buffer->prepend(\Charcoal\Buffers\Buffer::fromByteArray([0x63, 0x68, 0x61, 0x72]));
        $buffer->append(".dev");
        $this->assertEquals("charcoal.dev", $buffer->raw());
    }

    /**
     * @return void
     */
    public function testFlush(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer("abcd");
        $buffer->flush(); // Flush
        $this->assertEquals(0, $buffer->len());
    }

    /**
     * @return void
     */
    public function testFlushInReadOnlyMode(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer("abcd");
        $buffer->readOnly();
        $this->expectException('BadMethodCallException');
        $buffer->flush(); // Flush
    }
}