<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\streams
 */
namespace stubbles\streams;
/**
 * Interface for input streams.
 *
 * @api
 */
interface InputStream
{
    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read(int $length = 8192): string;

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine(int $length = 8192): string;

    /**
     * returns the amount of byted left to be read
     *
     * @return  int
     */
    public function bytesLeft(): int;

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof(): bool;

    /**
     * closes the stream
     */
    public function close();
}
