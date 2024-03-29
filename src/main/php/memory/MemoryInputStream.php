<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\memory;

use InvalidArgumentException;
use stubbles\streams\InputStream;
use stubbles\streams\Seekable;
/**
 * Class to stream data from memory.
 *
 * @api
 */
class MemoryInputStream implements InputStream, Seekable
{
    /**
     * current position in buffer
     */
    private int $position = 0;

    public function __construct(private string $buffer) { }

    /**
     * reads given amount of bytes
     */
    public function read(int $length = 8192): string
    {
        $bytes           = substr($this->buffer, $this->position, $length);
        $this->position += strlen($bytes);
        return $bytes;
    }

    /**
     * reads given amount of bytes or until next line break
     */
    public function readLine(int $length = 8192): string
    {
        $bytes        = substr($this->buffer, $this->position, $length);
        $linebreakpos = strpos($bytes, "\n");
        if (false !== $linebreakpos) {
            $line = substr($bytes, 0, $linebreakpos);
            $this->position += strlen($line) + 1;
        } else {
            $line = $bytes;
            $this->position += strlen($line);
        }

        return rtrim($line);
    }

    /**
     * returns the amount of byted left to be read
     */
    public function bytesLeft(): int
    {
        return strlen($this->buffer) - $this->position;
    }

    /**
     * returns true if the stream pointer is at EOF
     */
    public function eof(): bool
    {
        return strlen($this->buffer) === $this->position;
    }

    /**
     * closes the stream
     */
    public function close(): void
    {
        // intentionally empty
    }

    /**
     * seek to given offset
     *
     * @param  int $offset new position or amount of bytes to seek
     * @param  int $whence one of Seekable::SET, Seekable::CURRENT or Seekable::END
     * @throws InvalidArgumentException
     */
    public function seek(int $offset, int $whence = Seekable::SET): void
    {
        switch ($whence) {
            case Seekable::SET:
                $this->position = $offset;
                break;

            case Seekable::CURRENT:
                $this->position += $offset;
                break;

            case Seekable::END:
                $this->position = strlen($this->buffer) + $offset;
                break;

            default:
                throw new InvalidArgumentException(
                    'Wrong value for $whence, must be one of Seekable::SET,'
                    . ' Seekable::CURRENT or Seekable::END.'
                );
        }
    }

    /**
     * return current position
     */
    public function tell(): int
    {
        return $this->position;
    }
}
