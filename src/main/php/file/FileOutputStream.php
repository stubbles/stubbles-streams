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
use stubbles\streams\ResourceOutputStream;
use stubbles\streams\StreamException;

use function stubbles\streams\lastErrorMessage;
/**
 * Class for file based output streams.
 *
 * @api
 */
class FileOutputStream extends ResourceOutputStream
{
    protected string $fileName;

    /**
     * The delayed param only works in conjunction with the $file param being a
     * string. If set to true and the file does not exist creation of the file
     * will be delayed until first bytes should be written to the output stream.
     *
     * @param  string|resource $file
     * @param  string          $mode opening mode if $file is a filename
     * @throws InvalidArgumentException
     */
    public function __construct($file, private string $mode = 'wb', bool $delayed = false)
    {
        if (is_string($file)) {
            if (false === $delayed) {
                $this->setHandle($this->openFile($file, $mode));
            } else {
                $this->fileName = $file;
            }
        } elseif (is_resource($file) && get_resource_type($file) === 'stream') {
            $this->setHandle($file);
            $this->fileName = '<resource>';
        } else {
            throw new InvalidArgumentException(
                'File must either be a filename'
                . ' or an already opened file/stream resource.'
            );
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * writes given bytes
     *
     * @return int amount of written bytes
     */
    public function write(string $bytes): int
    {
        if ($this->isFileCreationDelayed()) {
            $this->setHandle($this->openFile($this->fileName, $this->mode));
        }

        return parent::write($bytes);
    }

    /**
     * checks whether file creation was delayed
     */
    protected function isFileCreationDelayed(): bool
    {
        return null === $this->handle && '<resource>' != $this->fileName;
    }

    /**
     * helper method to open a file handle
     *
     * @return resource
     * @throws StreamException
     */
    protected function openFile(string $file, string $mode)
    {
        $fp = @fopen($file, $mode);
        if (false === $fp) {
            throw new StreamException(
                'Can not open file ' . $file . ' with mode ' . $mode . ': '
                . str_replace('fopen(' . $file . '): ', '', lastErrorMessage())
            );
        }

        return $fp;
    }
}
