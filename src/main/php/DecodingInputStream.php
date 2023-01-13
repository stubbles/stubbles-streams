<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;

use function stubbles\streams\lastErrorMessage;
/**
 * Decodes input stream into internal charset.
 *
 * @api
 */
class DecodingInputStream extends DecoratedInputStream
{
    /**
     * @param string $charsetFrom charset of input stream
     * @param string $charsetTo   charset to decode to, defaults to UTF-8
     */
    public function __construct(
        InputStream $inputStream,
        private string $charsetFrom,
        private string $charsetTo = 'UTF-8'
    ) {
        parent::__construct($inputStream);
    }

    /**
     * returns charset of underlaying input stream
     */
    public function charset(): string
    {
        return $this->charsetFrom;
    }

    /**
     * reads given amount of bytes
     *
     * @throws StreamException when decoding fails due to illegal character in input stream
     */
    public function read(int $length = 8192): string
    {
        $decoded = @iconv(
            $this->charsetFrom,
            $this->charsetTo,
            $this->inputStream->read($length)
        );
        if (false === $decoded) {
            throw new StreamException(lastErrorMessage());
        }

        return $decoded;
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @throws StreamException when decoding fails due to illegal character in input stream
     */
    public function readLine(int $length = 8192): string
    {
        $decoded = @iconv(
            $this->charsetFrom,
            $this->charsetTo,
            $this->inputStream->readLine($length)
        );
        if (false === $decoded) {
            throw new StreamException(lastErrorMessage());
        }

        return $decoded;
    }
}
