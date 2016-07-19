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
namespace stubbles\streams {
    use stubbles\sequence\Sequence;
    use stubbles\streams\file\FileInputStream;

    /**
     * returns a sequence of lines from given input source
     *
     * @api
     * @param   \stubbles\streams\InputStream|string  $input
     * @return  \stubbles\sequence\Sequence
     * @since   5.2.0
     */
    function linesOf($input): Sequence
    {
        return Sequence::of(new InputStreamIterator(FileInputStream::castFrom($input)));
    }

    /**
     * returns a sequence of non empty lines from given input source
     *
     * @api
     * @param   \stubbles\streams\InputStream|string  $input
     * @return  \stubbles\sequence\Sequence
     * @since   6.2.0
     */
    function nonEmptyLinesOf($input): Sequence
    {
        return linesOf($input)->filter(function($line) { return !empty($line); });
    }

    /**
     * returns error message from last error that occurred
     *
     * @internal
     * @param   string  $default  optional  message to return in case no last error available
     * @return  string
     */
    function lastErrorMessage(string $default = null): string
    {
        $error = error_get_last();
        if (null === $error) {
            return $default;
        }

        return $error['message'];
    }
}
