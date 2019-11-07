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
    /**
     * input stream to encode into internal encoding
     *
     * @type  \stubbles\streams\InputStream
     */
    protected $inputStream;

    /**
     * constructor
     *
     * @param  \stubbles\streams\InputStream  $inputStream
     */
    public function __construct(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read(int $length = 8192): string
    {
        return $this->inputStream->read($length);
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine(int $length = 8192): string
    {
        return $this->inputStream->readLine($length);
    }

    /**
     * returns the amount of byted left to be read
     *
     * @return  int
     */
    public function bytesLeft(): int
    {
        return $this->inputStream->bytesLeft();
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof(): bool
    {
        return $this->inputStream->eof();
    }

    /**
     * closes the stream
     */
    public function close()
    {
        $this->inputStream->close();
    }
}
