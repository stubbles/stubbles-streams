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
 * Interface for output streams.
 *
 * @api
 */
interface OutputStream
{
    /**
     * writes given bytes
     *
     * @return int amount of written bytes
     */
    public function write(string $bytes): int;

    /**
     * writes given bytes and appends a line break
     *
     * @return int amount of written bytes
     */
    public function writeLine(string $bytes): int;

    /**
     * writes given bytes and appends a line break after each one
     *
     * @param  string[] $bytes
     * @return int      amount of written bytes
     * @since  3.2.0
     */
    public function writeLines(array $bytes): int;

    /**
     * closes the stream
     */
    public function close(): void;
}
