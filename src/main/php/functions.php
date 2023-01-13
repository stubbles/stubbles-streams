<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams {
    use stubbles\sequence\Sequence;
    use stubbles\streams\file\FileInputStream;

    /**
     * returns a sequence of lines from given input source
     *
     * @api
     * @return Sequence<string>
     * @since  5.2.0
     */
    function linesOf(InputStream|string $input): Sequence
    {
        return Sequence::of(new InputStreamIterator(FileInputStream::castFrom($input)));
    }

    /**
     * returns a sequence of non empty lines from given input source
     *
     * @api
     * @return Sequence<string>
     * @since  6.2.0
     */
    function nonEmptyLinesOf(InputStream|string $input): Sequence
    {
        return linesOf($input)->filter(fn($line) => !empty($line));
    }

    /**
     * returns error message from last error that occurred
     *
     * @internal
     */
    function lastErrorMessage(string $default = ''): string
    {
        $error = error_get_last();
        if (null === $error) {
            return $default;
        }

        return $error['message'] ?? '';
    }

    /**
     * creates a copier which allows to copy all lines from given input stream to an output stream
     *
     * Please note that copying starts at the offset where the input stream
     * currently is located, and changes the offset of the input stream to the
     * end of the stream. In case a non-seekable input stream is copied it can
     * not return to its initial offset.
     *
     * @since 8.1.0
     */
    function copy(InputStream $from): Copier
    {
        return new Copier($from);
    }

    /**
     * @internal
     * @since 8.1.0
     */
    final class Copier
    {
        public function __construct(private InputStream $source) { }

        /**
         * copies into given output stream
         *
         * @return int amount of bytes copied
         */
         public function to(OutputStream $target): int
         {
             $copiedBytes = 0;
             while (!$this->source->eof()) {
                 $copiedBytes += $target->write($this->source->read());
             }

             return $copiedBytes;
         }
    }
}
