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
namespace stubbles\streams;
/**
 * Iterator for input streams.
 *
 * @api
 * @since  5.2.0
 */
class InputStreamIterator implements \Iterator
{
    /**
     * input stream to iterate on
     *
     * @type  \stubbles\streams\InputStream
     */
    private $inputStream;
    /**
     * current line
     *
     * @type  string
     */
    private $currentLine;
    /**
     * current line number
     *
     * @type  int
     */
    private $lineNumber = 0;

    /**
     * constructor
     *
     * @param   \stubbles\streams\InputStream  $inputStream
     * @throws  \InvalidArgumentException  in case input stream is not seekable
     */
    public function __construct(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
        $this->next();
    }

    /**
     * returns the current line
     *
     * @return  string
     */
    public function current(): string
    {
        return $this->currentLine;
    }

    /**
     * returns current line number
     *
     * @return  int
     */
    public function key(): int
    {
        return $this->lineNumber;
    }

    /**
     * moves forward to next line
     */
    public function next()
    {
        $this->currentLine = $this->inputStream->readLine();
        $this->lineNumber++;
    }

    /**
     * rewinds to first line
     */
    public function rewind()
    {
        if (!($this->inputStream instanceof Seekable)) {
            return;
        }

        $this->inputStream->seek(0, Seekable::SET);
        $this->lineNumber  = 0;
        $this->currentLine = null;
        $this->next();
    }

    /**
     * checks if current element is valid
     *
     * @return  bool
     */
    public function valid(): bool
    {
        return !$this->inputStream->eof();
    }
}
