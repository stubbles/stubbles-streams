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
 * Decodes input stream into internal charset.
 *
 * @api
 */
class DecodingInputStream extends DecoratedInputStream
{
    /**
     * @type  string
     */
    private $charset;

    /**
     * constructor
     *
     * @param  \stubbles\streams\InputStream  $inputStream
     * @param  string                         $charset      charset of input stream
     */
    public function __construct(InputStream $inputStream, string $charset)
    {
        parent::__construct($inputStream);
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
     * returns charset of input stream
     *
     * @return  string
     * @deprecated  since 8.0.0, use charset() instead, will be removed with 9.0.0
     */
    public function getCharset(): string
    {
        return $this->charset();
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read(int $length = 8192): string
    {
        return iconv($this->charset, 'UTF-8', $this->inputStream->read($length));
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine(int $length = 8192): string
    {
        return iconv($this->charset, 'UTF-8', $this->inputStream->readLine($length));
    }
}
