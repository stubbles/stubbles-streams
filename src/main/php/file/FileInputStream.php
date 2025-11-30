<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\file;

use InvalidArgumentException;
use LogicException;
use stubbles\streams\InputStream;
use stubbles\streams\ResourceInputStream;
use stubbles\streams\Seekable;
use stubbles\streams\StreamException;
use stubbles\streams\Whence;

use function stubbles\streams\lastErrorMessage;
/**
 * Class for file based input streams.
 *
 * @api
 */
class FileInputStream extends ResourceInputStream implements Seekable
{
    protected string $fileName;

    /**
     * @param  string|resource $file
     * @param  string          $mode opening mode if $file is a filename
     * @throws StreamException
     * @throws InvalidArgumentException
     */
    public function __construct($file, string $mode = 'rb')
    {
        if (is_string($file)) {
            $fp = @fopen($file, $mode);
            if (false === $fp) {
                throw new StreamException(
                    'Can not open file ' . $file . ' with mode ' . $mode. ': '
                    . str_replace('fopen(' . $file . '): ', '', lastErrorMessage())
                );
            }

            $this->fileName = $file;
        } elseif (is_resource($file) && get_resource_type($file) === 'stream') {
            $fp = $file;
            $this->fileName = '<resource>';
        } else {
            throw new InvalidArgumentException(
                'File must either be a filename'
                . ' or an already opened file/stream resource.'
            );
        }

        $this->setHandle($fp);
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * casts given value to an input stream
     *
     * @since 5.2.0
     */
    public static function castFrom(InputStream|string $value): InputStream
    {
        if ($value instanceof InputStream) {
            return $value;
        }

        return new self($value);
    }

    /**
     * helper method to retrieve the length of the resource
     *
     * @throws StreamException
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
     * Note: passing an int value for $whence is deprecated since 11.0.0.
     * Use enum Whence instead.
     *
     * @throws LogicException  when trying to seek on an already closed stream
     * @throws StreamException when seeking fails
     */
    public function seek(int $offset, int|Whence $whence = Whence::SET): void
    {
        if (!is_resource($this->handle)) {
            throw new LogicException('Can not read from closed input stream.');
        }

        if (-1 === fseek($this->handle, $offset, Whence::castFrom($whence)->value)) {
            throw new StreamException('Could not seek');
        }
    }

    /**
     * return current position
     *
     * @throws LogicException
     * @throws StreamException
     */
    public function tell(): int
    {
        if (!is_resource($this->handle)) {
            throw new LogicException('Can not read from closed input stream.');
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
