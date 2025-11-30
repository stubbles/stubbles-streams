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
     *
     * @deprecated use Whence::SET instead, will be removed with 12.0
     */
    public const Whence SET = Whence::SET;
    /**
     * set position to current location plus offset
     *
     * @deprecated use Whence::CURRENT instead, will be removed with 12.0
     */
    public const Whence CURRENT = Whence::CURRENT;
    /**
     * set position to end-of-file plus offset
     *
     * @deprecated use Whence::END instead, will be removed with 12.0
     */
    public const Whence END = Whence::END;

    /**
     * seek to given offset
     *
     * Note: passing an int value for $whence is deprecated since 11.0.0.
     * Use enum Whence instead.
     *
     * @param  int $offset offset to seek to
     * @param  int|Whence $whence optional  one of Whence::SET, Whence::CURRENT or Whence::END
     * @throws \LogicException in case the stream was already closed
     */
    public function seek(int $offset, int|Whence $whence = Whence::SET): void;

    /**
     * return current position
     *
     * @throws \LogicException in case the stream was already closed
     */
    public function tell(): int;
}
