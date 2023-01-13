<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;

use Iterator;
/**
 * Iterator for input streams.
 *
 * @api
 * @since  5.2.0
 * @implements Iterator<int,string>
 */
class InputStreamIterator implements Iterator
{
    private string $currentLine = '';
    private int $lineNumber = 0;
    private bool $valid = true;

    public function __construct(private InputStream $inputStream)
    {
        $this->next();
    }

    /**
     * returns the current line
     */
    public function current(): string
    {
        return $this->currentLine;
    }

    /**
     * returns current line number
     */
    public function key(): int
    {
        return $this->lineNumber;
    }

    /**
     * moves forward to next line
     */
    public function next(): void
    {
        $this->valid       = !$this->inputStream->eof();
        $this->currentLine = $this->inputStream->readLine();
        $this->lineNumber++;
    }

    /**
     * rewinds to first line
     */
    public function rewind(): void
    {
        if (!($this->inputStream instanceof Seekable)) {
            return;
        }

        $this->inputStream->seek(0, Seekable::SET);
        $this->lineNumber  = 0;
        $this->currentLine = '';
        $this->next();
    }

    /**
     * checks if current element is valid
     */
    public function valid(): bool
    {
        return $this->valid;
    }
}
