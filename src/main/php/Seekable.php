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
 * A seekable stream may be altered in its position to read data.
 *
 * @api
 */
interface Seekable
{
    /**
     * set position equal to offset  bytes
     */
    public const SET     = SEEK_SET;
    /**
     * set position to current location plus offset
     */
    public const CURRENT = SEEK_CUR;
    /**
     * set position to end-of-file plus offset
     */
    public const END     = SEEK_END;

    /**
     * seek to given offset
     *
     * @param  int $offset offset to seek to
     * @param  int $whence optional  one of Seekable::SET, Seekable::CURRENT or Seekable::END
     * @throws \LogicException in case the stream was already closed
     */
    public function seek(int $offset, int $whence = Seekable::SET): void;

    /**
     * return current position
     *
     * @throws \LogicException in case the stream was already closed
     */
    public function tell(): int;
}
