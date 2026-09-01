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
 * Class ByteDigestTest
 */
class ByteDigestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Verify basic hashes
     * @return void
     */
    public function testHashes(): void
    {
        $buffer = new \Charcoal\Buffers\Buffer("charcoal.dev");

        $b1md5 = hash("md5", "charcoal.dev", true);
        $b1sha1 = hash("sha1", "charcoal.dev", false);
        $b1sha256 = hash("sha256", "charcoal.dev", true);

        $this->assertEquals($b1md5, $buffer->hash()->md5()->raw());
        $this->assertEquals($b1sha1, $buffer->hash()->sha1()->toBase16());
        $this->assertEquals($b1sha256, $buffer->hash()->sha256()->raw());
    }

    /**
     * Getting result as string with "returnString" arg while "toString" method set
     * @return void
     */
    public function testResultAsString(): void
    {
        $digest = (new Charcoal\Buffers\Buffer("charcoal.dev"))->hash();
        $digest->toString(); // All result should be string

        $this->assertIsString($digest->hash("sha1"));
        $this->assertIsObject($digest->hash("sha1", returnString: false)); // Passing returnString arg overrides toString method
    }

    /**
     * Getting result as string with "returnString" arg while "toString" method NOT set
     * @return void
     */
    public function testResultAsString2(): void
    {
        $digest = (new Charcoal\Buffers\Buffer("charcoal.dev"))->hash(); // All result should be objects

        $this->assertIsNotString($digest->hash("sha1"));
        $this->assertIsString($digest->hash("sha1", returnString: true)); // Passing returnString arg overrides toString method
        $this->assertIsObject($digest->hash("sha1"));
    }

    /**
     * Verify return types of various methods
     * - Methods "hash", "pbkdf2", "hmac" must return instance of Buffer (set as readOnly)
     * - Methods "md5", "sha1", "sha256", "ripeMd160" must return fixed length frames buffer
     * - Method "sha512" will return Buffer (set as readOnly)
     * @return void
     */
    public function testReturnObjects(): void
    {
        $digest = (new Charcoal\Buffers\Buffer("charcoal.dev"))->hash();
        $this->assertInstanceOf(\Charcoal\Buffers\Buffer::class, $digest->hash("sha1"));
        $this->assertInstanceOf(\Charcoal\Buffers\Frames\Bytes20::class, $digest->sha1());
        $this->assertInstanceOf(\Charcoal\Buffers\Frames\Bytes32::class, $digest->sha256());
        $this->assertInstanceOf(\Charcoal\Buffers\Frames\Bytes20::class, $digest->ripeMd160());
        $this->assertInstanceOf(\Charcoal\Buffers\Frames\Bytes16::class, $digest->md5());

        $this->assertInstanceOf(\Charcoal\Buffers\Buffer::class, $digest->hmac("sha256", new \Charcoal\Buffers\Buffer('some-key')));
        $this->assertInstanceOf(\Charcoal\Buffers\Buffer::class, $digest->pbkdf2("sha256", "random-salt", 100));
    }
}