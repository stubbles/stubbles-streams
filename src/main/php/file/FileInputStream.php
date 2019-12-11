<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\file;
use stubbles\streams\InputStream;
use stubbles\streams\ResourceInputStream;
use stubbles\streams\Seekable;
use stubbles\streams\StreamException;

use function stubbles\streams\lastErrorMessage;
/**
 * Class for file based input streams.
 *
 * @api
 */
class FileInputStream extends ResourceInputStream implements Seekable
{
    /**
     * name of the file
     *
     * @type  string
     */
    protected $fileName;

    /**
     * constructor
     *
     * @param   string|resource  $file
     * @param   string           $mode  opening mode if $file is a filename
     * @throws  \stubbles\streams\StreamException
     * @throws  \InvalidArgumentException
     */
    public function __construct($file, string $mode = 'rb')
    {
        if (is_string($file)) {
            $fp = @fopen($file, $mode);
            if (false === $fp) {
                throw new StreamException(
                        'Can not open file ' . $file . ' with mode ' . $mode. ': '
                        .
                        str_replace('fopen(' . $file . '): ', '', lastErrorMessage())
                );
            }

            $this->fileName = $file;
        } elseif (is_resource($file) && get_resource_type($file) === 'stream') {
            $fp = $file;
        } else {
            throw new \InvalidArgumentException(
                    'File must either be a filename'
                    . ' or an already opened file/stream resource.'
            );
        }

        $this->setHandle($fp);
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * casts given value to an input stream
     *
     * @param   \stubbles\streams\InputStream|string  $value
     * @return  \stubbles\streams\InputStream
     * @throws  \InvalidArgumentException
     * @since   5.2.0
     */
    public static function castFrom($value): InputStream
    {
        if ($value instanceof InputStream) {
            return $value;
        }

        if (is_string($value)) {
            return new self($value);
        }

        throw new \InvalidArgumentException(
                'Given value is neither an instance of' . InputStream::class
                . ' nor a string denoting a file'
        );
    }

    /**
     * helper method to retrieve the length of the resource
     *
     * @return  int
     */
    protected function getResourceLength(): int
    {
        if (null === $this->fileName) {
            return parent::getResourceLength();
        }

        if (substr($this->fileName, 0, 16) === 'compress.zlib://') {
            $size = filesize(substr($this->fileName, 16));
            if (false === $size) {
                throw new StreamException('Can not determine resource length of ' . $this->fileName);
            }

            return $size;
        } elseif (substr($this->fileName, 0, 17) === 'compress.bzip2://') {
          $size = filesize(substr($this->fileName, 17));
          if (false === $size) {
              throw new StreamException('Can not determine resource length of ' . $this->fileName);
          }

          return $size;
        }

        return parent::getResourceLength();
    }

    /**
     * seek to given offset
     *
     * @param   int  $offset
     * @param   int  $whence  one of Seekable::SET, Seekable::CURRENT or Seekable::END
     * @throws  \LogicException  when trying to seek on an already closed stream
     * @throws  StreamException  when seeking fails
     */
    public function seek(int $offset, int $whence = Seekable::SET)
    {
        if (null === $this->handle) {
            throw new \LogicException('Can not read from closed input stream.');
        }

        if (-1 === fseek($this->handle, $offset, $whence)) {
            throw new StreamException('Could not seek');
        }
    }

    /**
     * return current position
     *
     * @return  int
     * @throws  \LogicException
     * @throws  \stubbles\streams\StreamException
     */
    public function tell(): int
    {
        if (null === $this->handle) {
            throw new \LogicException('Can not read from closed input stream.');
        }

        $position = @ftell($this->handle);
        if (false === $position) {
            throw new StreamException(
                    'Can not read current position in file: '
                    . lastErrorMessage('unknown error')
            );
        }

        return $position;
    }
}
