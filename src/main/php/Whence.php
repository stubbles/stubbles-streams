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

/**
 * @since 11.0.0
 */
enum Whence: int
{
    /** Set position equal to offset bytes from the start of the file. */
    case SET = SEEK_SET;
    /**  Set position to current location plus offset bytes. */
    case CURRENT = SEEK_CUR;
    /** Set position to end-of-file plus offset bytes. */
    case END = SEEK_END;

    /**
     * @deprecated since 11.0.0, will be removed with 12.0.0
     * @throws ValueError in case given int is not a valid value
     */
    public static function castFrom(int|Whence $value): Whence
    {
        if ($value instanceof Whence) {
            return $value;
        }

        $whence = Whence::tryFrom($value);
        if (null === $whence) {
            throw new InvalidArgumentException(
                'Wrong value for $whence, must be one of Seekable::SET,'
                . ' Seekable::CURRENT or Seekable::END.'
            );
        }

        return $whence;
    }
}
