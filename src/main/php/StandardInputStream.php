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
 * Input stream for reading from php://input.
 *
 * @since  5.4.0
 */
class StandardInputStream extends ResourceInputStream implements Seekable
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->setHandle(fopen('php://input', 'rb'));
    }

    /**
     * seek to given offset
     *
     * @param   int  $offset  offset to seek to
     * @param   int  $whence  optional  one of Seekable::SET, Seekable::CURRENT or Seekable::END
     * @throws  \LogicException  in case the stream was already closed
     */
    public function seek(int $offset, int $whence = Seekable::SET)
    {
        if (null === $this->handle) {
            throw new \LogicException('Can not seek on closed input stream.');
        }

        fseek($this->handle, $offset, $whence);
    }

    /**
     * return current position
     *
     * @return  int
     * @throws  \LogicException  in case the stream was already closed
     * @throws  \stubbles\streams\StreamException
     */
    public function tell(): int
    {
        if (null === $this->handle) {
            throw new \LogicException('No position available for closed input stream');
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
