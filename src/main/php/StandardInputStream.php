<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;

use LogicException;
use RuntimeException;

/**
 * Input stream for reading from php://input.
 *
 * @since  5.4.0
 */
class StandardInputStream extends ResourceInputStream implements Seekable
{
    public function __construct()
    {
        $fp = fopen('php://input', 'rb');
        if (false === $fp) {
            throw new RuntimeException('Could not open input stream');
        }

        $this->setHandle($fp);
    }

    /**
     * seek to given offset
     *
     * Note: passing an int value for $whence is deprecated since 11.0.0.
     * Use enum Whence instead.
     *
     * @param  int $offset offset to seek to
     * @param  int|Whence $whence optional one of Whence::SET, Whence::CURRENT or Whence::END
     * @throws LogicException in case the stream was already closed
     * @throws StreamException when seeking fails
     */
    public function seek(int $offset, int|Whence $whence = Whence::SET): void
    {
        if (!is_resource($this->handle)) {
            throw new LogicException('Can not seek on closed input stream.');
        }

        if (-1 === fseek($this->handle, $offset, Whence::castFrom($whence)->value)) {
            throw new StreamException('Could not seek');
        }
    }

    /**
     * return current position
     *
     * @throws LogicException in case the stream was already closed
     * @throws StreamException
     */
    public function tell(): int
    {
        if (!is_resource($this->handle)) {
            throw new LogicException('No position available for closed input stream');
        }

        $position = @ftell($this->handle);
        if (false === $position) {
            throw new StreamException(
                    'Can not read current position in php://input: '
                    . lastErrorMessage('unknown error')
            );
        }

        return $position;
    }
}
