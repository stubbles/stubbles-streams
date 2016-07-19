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
 * Encodes internal encoding into output charset.
 *
 * @api
 */
class EncodingOutputStream extends DecoratedOutputStream
{
    /**
     * @type  string
     */
    private $charset;

    /**
     * constructor
     *
     * @param  \stubbles\streams\OutputStream  $outputStream
     * @param  string                          $charset       charset of output stream
     */
    public function __construct(OutputStream $outputStream, string $charset)
    {
        parent::__construct($outputStream);
        $this->charset = $charset;
    }

    /**
     * returns charset of input stream
     *
     * @return  string
     */
    public function charset(): string
    {
        return $this->charset;
    }

    /**
     * returns charset of output stream
     *
     * @return  string
     * @deprecated  since 8.0.0, use charset() instead, will be removed with 9.0.0
     */
    public function getCharset(): string
    {
        return $this->charset();
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write(string $bytes): int
    {
        return $this->outputStream->write(iconv('UTF-8', $this->charset, $bytes));
    }

    /**
     * writes given bytes and appends a line break after each one
     *
     * @param   string[]  $bytes
     * @return  int       amount of written bytes
     * @since   3.2.0
     */
    public function writeLines(array $bytes): int
    {
        $bytesWritten = 0;
        foreach ($bytes as $line) {
            $bytesWritten += $this->writeLine($line);
        }

        return $bytesWritten;

    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine(string $bytes): int
    {
        return $this->outputStream->writeLine(iconv('UTF-8', $this->charset, $bytes));
    }
}
