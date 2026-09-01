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
 * Class ByteReaderTests
 */
class ByteReaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testByteReader(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer("\0furqan\1\2" . chr(0xfd) . "\3" .
            \Charcoal\Buffers\ByteOrder\LittleEndian::PackUInt16(0xfffe) . "\t\r\nsiddiqui"
        );

        $bytes = $buffer->read();
        $this->assertFalse($bytes->isEnd());
        $this->assertEquals("\0fu", $bytes->first(3));
        $this->assertEquals("rq", $bytes->next(2));
        $this->assertEquals("\0f", $bytes->first(2), "Testing reset on 'first' method call");
        $this->assertEquals("urqa", $bytes->lookAhead(4), "Look ahead method test");
        $this->assertEquals("ur", $bytes->next(2), "Making sure pointer was not changed by lookAhead call");
        $bytes->next(3); // ignore next 3 bytes
        $this->assertEquals("furqan", $bytes->lookBehind(6), "Testing look behind method");
        $this->assertEquals("\1\2", $bytes->next(2), "Making sure pointer was not changed by lookBehind call");
        $this->assertEquals(253, $bytes->readUInt8(), "Single byte integer");
        $this->assertEquals("\3", $bytes->next(1));
        $this->assertEquals(65534, $bytes->readUInt16LE(), "Two byte integer");
        $this->assertFalse($bytes->isEnd());
        $this->assertEquals(9, $bytes->readUInt8());
        $this->assertEquals("\t", $bytes->lookBehind(1));
        $bytes->next(2); // ignore next 2 bytes
        $this->assertEquals("siddiqui", $bytes->remaining(), "Using remaining method call will not update pointer");
        $this->assertFalse($bytes->isEnd());
        $bytes->next(8); // ignore next 8 bytes
        $this->assertTrue($bytes->isEnd());
    }

    /**
     * @return void
     */
    public function testUnderflowException(): void
    {
        $bytes = (new \Charcoal\Buffers\Buffer("charcoal.dev"))->read();
        $this->assertEquals("charcoal", $bytes->next(8));
        $this->assertEquals(".", $bytes->next(1));
        $this->expectException('UnderflowException');
        $bytes->next(4); // Attempt to read 4 bytes while only 3 remaining
    }

    /**
     * Test functionality when ignoring UnderflowException.
     *  - This is not recommended.
     *  - Implementations should always handle UnderflowException.
     * @return void
     */
    public function testIgnoreUnderflowException(): void
    {
        $bytes = (new \Charcoal\Buffers\Buffer("charcoal.dev"))->read();
        $bytes->throwUnderflowEx = false; // Disable UnderflowException

        $this->assertEquals("charcoal", $bytes->next(8));
        $this->assertEquals(".", $bytes->next(1));
        $this->assertFalse($bytes->isEnd());
        $this->assertEquals("dev", $bytes->next(100)); // Asked for next 100 bytes while only 3 are there
        $this->assertTrue($bytes->isEnd()); // This will mark buffer as finished
    }

    /**
     * @return void
     */
    public function testBytesLeft(): void
    {
        $bytes = (new \Charcoal\Buffers\Buffer("charcoal-dev"))->read();
        $bytes->first(4); // skip 4 bytes
        $this->assertEquals(8, $bytes->bytesLeft());
        $bytes->next(4); // skip another 4
        $bytes->readUInt8(); // skip another 1
        $this->assertEquals(3, $bytes->bytesLeft());
        $bytes->next(3);
        $this->assertEquals(0, $bytes->bytesLeft());
    }

    /**
     * @return void
     */
    public function testBytesLeftWithNoUnderflowEx(): void
    {
        $bytes = (new \Charcoal\Buffers\Buffer("charcoal-dev"))->read();
        $bytes->throwUnderflowEx = false;

        $bytes->first(8); // skip 8 bytes
        $this->assertEquals(4, $bytes->bytesLeft());
        $bytes->readUInt16LE(); // skip 2 bytes
        $this->assertEquals(2, $bytes->bytesLeft());
        $bytes->next(5);
        $this->assertEquals(0, $bytes->bytesLeft());
    }

    /**
     * @return void
     */
    public function testBytesLeft2(): void
    {
        $bytes = (new \Charcoal\Buffers\Buffer("charcoal"))->read();
        $bytes->first(2); // 2
        $bytes->readUInt16LE(); // +2 = 4
        $this->assertEquals("coal", $bytes->next($bytes->bytesLeft()));
        $this->assertTrue($bytes->isEnd());
    }
}