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

use Charcoal\Buffers\Frames\Bytes16;
use Charcoal\Buffers\Frames\Bytes20;
use Charcoal\Buffers\Frames\Bytes32;
use Charcoal\Buffers\Frames\Bytes64;

/**
 * Class ByteDigest
 * @package Charcoal\Buffers
 */
class ByteDigest
{
    /** @var string */
    private string $val;
    /** @var bool */
    private bool $returnBuffer = true;

    /**
     * ByteDigest constructor.
     * @param AbstractByteArray $bA
     */
    public function __construct(AbstractByteArray $bA)
    {
        $this->val = $bA->raw();
    }

    /**
     * @return $this
     */
    public function toString(): static
    {
        $this->returnBuffer = false;
        return $this;
    }

    /**
     * @param string $algo
     * @param int $iterations
     * @param int|null $len
     * @param bool|null $returnString
     * @return string|\Charcoal\Buffers\Buffer
     */
    public function hash(string $algo, int $iterations = 1, ?int $len = null, ?bool $returnString = null): string|Buffer
    {
        if (!in_array($algo, hash_algos())) {
            throw new \OutOfBoundsException('Invalid/unsupported hash algorithm');
        }

        $digest = $this->val;
        for ($i = 0; $i < $iterations; $i++) {
            $digest = hash($algo, $digest, true);
        }

        if ($len > 0) {
            $digest = substr($digest, 0, $len);
        }

        return $this->result($digest, $returnString);
    }

    /**
     * @param string $algo
     * @param \Charcoal\Buffers\AbstractByteArray|string $key
     * @param bool|null $returnString
     * @return string|\Charcoal\Buffers\Buffer
     */
    public function hmac(string $algo, AbstractByteArray|string $key, ?bool $returnString = null): string|Buffer
    {
        if (!in_array($algo, hash_hmac_algos())) {
            throw new \OutOfBoundsException('Invalid/unsupported hmac algorithm');
        }

        $key = $key instanceof AbstractByteArray ? $key->raw() : $key;
        return $this->result(hash_hmac($algo, $this->val, $key, true), $returnString);
    }

    /**
     * @param string $algo
     * @param \Charcoal\Buffers\AbstractByteArray|string $salt
     * @param int $iterations
     * @param int $len
     * @param bool|null $returnString
     * @return string|\Charcoal\Buffers\Buffer
     */
    public function pbkdf2(string $algo, AbstractByteArray|string $salt, int $iterations, int $len = 0, ?bool $returnString = null): string|Buffer
    {
        if (!in_array($algo, hash_algos())) {
            throw new \OutOfBoundsException('Invalid/unsupported hash (pbkdf2) algorithm');
        }

        $salt = $salt instanceof AbstractByteArray ? $salt->raw() : $salt;
        return $this->result(hash_pbkdf2($algo, $this->val, $salt, $iterations, $len, true), $returnString);
    }

    /**
     * @return \Charcoal\Buffers\Frames\Bytes16
     */
    public function md5(): Bytes16
    {
        return new Bytes16($this->hash("md5", returnString: true));
    }

    /**
     * @return \Charcoal\Buffers\Frames\Bytes20
     */
    public function sha1(): Bytes20
    {
        return new Bytes20($this->hash("sha1", returnString: true));
    }

    /**
     * @return \Charcoal\Buffers\Frames\Bytes32
     */
    public function sha256(): Bytes32
    {
        return new Bytes32($this->hash("sha256", returnString: true));
    }

    /**
     * @return \Charcoal\Buffers\Frames\Bytes64
     */
    public function sha512(): Bytes64
    {
        return new Bytes64($this->hash("sha512", returnString: true));
    }

    /**
     * @return \Charcoal\Buffers\Frames\Bytes20
     */
    public function ripeMd160(): Bytes20
    {
        return new Bytes20($this->hash("ripemd160", returnString: true));
    }

    /**
     * @param string $raw
     * @param bool|null $returnStr
     * @return string|\Charcoal\Buffers\Buffer
     */
    private function result(string $raw, ?bool $returnStr = null): string|Buffer
    {
        if (is_bool($returnStr)) {
            return $returnStr ? $raw : (new Buffer($raw))->readOnly();
        }

        return $this->returnBuffer ? (new Buffer($raw))->readOnly() : $raw;
    }
}
