<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;
/**
 * Abstract base class for decorated input streams.
 *
 * @api
 */
abstract class DecoratedInputStream implements InputStream
{
    public function __construct(protected InputStream $inputStream) { }

    /**
     * reads given amount of bytes
     */
    public function read(int $length = 8192): string
    {
        return $this->inputStream->read($length);
    }

    /**
     * reads given amount of bytes or until next line break
     */
    public function readLine(int $length = 8192): string
    {
        return $this->inputStream->readLine($length);
    }

    /**
     * returns the amount of byted left to be read
     */
    public function bytesLeft(): int
    {
        return $this->inputStream->bytesLeft();
    }

    /**
     * returns true if the stream pointer is at EOF
     */
    public function eof(): bool
    {
        return $this->inputStream->eof();
    }

    /**
     * closes the stream
     */
    public function close(): void
    {
        $this->inputStream->close();
    }
}
