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
 * Encodes internal encoding into output charset.
 *
 * @api
 */
class EncodingOutputStream extends DecoratedOutputStream
{
    /**
     * @var  string
     */
    private $charsetTo;
    /**
     * @var  string
     */
    private $charsetFrom;

    /**
     * constructor
     *
     * @param  \stubbles\streams\OutputStream  $outputStream
     * @param  string                          $charsetTo     charset of output stream
     * @param  string                          $charsetFrom   charset of given data to write, defaults to UTF-8
     */
    public function __construct(OutputStream $outputStream, string $charsetTo, string $charsetFrom = 'UTF-8')
    {
        parent::__construct($outputStream);
        $this->charsetTo   = $charsetTo;
        $this->charsetFrom = $charsetFrom;
    }

    /**
     * returns charset of input stream
     *
     * @return  string
     */
    public function charset(): string
    {
        return $this->charsetTo;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     * @throws  StreamException  in case encoding of given bytes failed
     */
    public function write(string $bytes): int
    {
        $encoded = @iconv($this->charsetFrom, $this->charsetTo, $bytes);
        if (false === $encoded) {
            throw new StreamException(lastErrorMessage());
        }

        return $this->outputStream->write($encoded);
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
     * @throws  StreamException  in case encoding of given bytes failed
     */
    public function writeLine(string $bytes): int
    {
      $encoded = @iconv($this->charsetFrom, $this->charsetTo, $bytes);
      if (false === $encoded) {
          throw new StreamException(lastErrorMessage());
      }

      return $this->outputStream->writeLine($encoded);
    }
}
