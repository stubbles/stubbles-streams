<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;

use InvalidArgumentException;
use LogicException;

/**
 * Class for resource based output streams.
 *
 * @internal
 */
abstract class ResourceOutputStream implements OutputStream
{
    /**
     * the descriptor for the stream
     *
     * @var  resource|null
     */
    protected $handle;

    /**
     * sets the resource to be used
     *
     * @param  resource $handle
     * @throws InvalidArgumentException
     */
    protected function setHandle($handle): void
    {
        if (!is_resource($handle)) {
            throw new InvalidArgumentException(
                'Handle needs to be a stream resource.'
            );
        }

        $this->handle = $handle;
    }

    /**
     * writes given bytes
     *
     * @return int    amount of written bytes
     * @throws LogicException
     * @throws StreamException
     */
    public function write(string $bytes): int
    {
        if (!is_resource($this->handle)) {
            throw new LogicException('Can not write to closed output stream.');
        }

        $length = @fwrite($this->handle, $bytes);
        if (false === $length) {
            throw new StreamException(
                'Can not write to output stream:' . lastErrorMessage('unknown error')
            );
        }

        return $length;
    }

    /**
     * writes given bytes and appends a line break
     *
     * @return int amount of written bytes
     */
    public function writeLine(string $bytes): int
    {
        return $this->write($bytes . "\r\n");
    }

    /**
     * writes given bytes and appends a line break after each one
     *
     * @param  string[] $bytes
     * @return int      amount of written bytes
     * @since  3.2.0
     */
    public function writeLines(array $bytes): int
    {
        $bytesWritten = 0;
        foreach ($bytes as $line) {
            $bytesWritten += $this->writeLine($line);
        }

        return $bytesWritten;

    }

    /**
     * closes the stream
     */
    public function close(): void
    {
        if (null !== $this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
