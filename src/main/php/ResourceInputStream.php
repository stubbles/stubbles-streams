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
 * Class for resource based input streams.
 *
 * @internal
 */
abstract class ResourceInputStream implements InputStream
{
    /**
     * the descriptor for the stream
     *
     * @var  resource|null
     */
    protected $handle;

    /**
     * sets the resource to be used
     *
     * @param   resource  $handle
     * @throws  \InvalidArgumentException
     */
    protected function setHandle($handle): void
    {
        if (!is_resource($handle)) {
            throw new \InvalidArgumentException(
                    'Handle needs to be a stream resource.'
            );
        }

        $this->handle = $handle;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     * @throws  \LogicException
     * @throws  \stubbles\streams\StreamException
     */
    public function read(int $length = 8192): string
    {
        return $this->doRead('fread', $length);
    }

    /**
     * reads given amount of bytes or until next line break and removes line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     * @throws  \LogicException
     * @throws  \stubbles\streams\StreamException
     */
    public function readLine(int $length = 8192): string
    {
        return rtrim($this->doRead('fgets', $length), "\n\r");
    }

    /**
     * do actual read
     *
     * @param   callable  $read    function to use for reading from handle
     * @param   int       $length  max amount of bytes to read
     * @return  string
     * @throws  \LogicException
     * @throws  \stubbles\streams\StreamException
     */
    private function doRead(callable $read, int $length): string
    {
        if (null === $this->handle) {
            throw new \LogicException('Can not read from closed input stream.');
        }

        $data = @$read($this->handle, $length);
        if (false === $data) {
            $error = lastErrorMessage('unknown error');
            if (!@feof($this->handle)) {
                throw new StreamException(
                        'Can not read from input stream: ' . $error
                );
            }

            return '';
        }

        return $data;
    }

    /**
     * returns the amount of bytes left to be read
     *
     * @return  int
     * @throws  \LogicException
     */
    public function bytesLeft(): int
    {
        if (null === $this->handle || !is_resource($this->handle)) {
            throw new \LogicException('Can not read from closed input stream.');
        }

        $bytesRead = ftell($this->handle);
        if (!is_int($bytesRead)) {
            return 0;
        }

        return $this->getResourceLength() - $bytesRead;
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     * @throws  \LogicException  when trying to check eof of already closed stream
     */
    public function eof(): bool
    {
        if (null === $this->handle || !is_resource($this->handle)) {
            throw new \LogicException('Can not check eof of closed input stream.');
        }

        return feof($this->handle);
    }

    /**
     * helper method to retrieve the length of the resource
     *
     * Not all stream wrappers support (f)stat - the extending class then
     * needs to take care to deliver the correct resource length then.
     *
     * @return  int
     * @throws  \LogicException  when trying to get resource length of already closed stream
     * @throws  StreamException  when retrieving stat data fails
     */
    protected function getResourceLength(): int
    {
        if (null === $this->handle || !is_resource($this->handle)) {
            throw new \LogicException('Can not read from closed input stream.');
        }

        $fileData = fstat($this->handle);
        if (false === $fileData) {
            throw new StreamException('Could not retrieve stat data');
        }

        return (int) $fileData['size'];
    }

    /**
     * closes the stream
     */
    public function close(): void
    {
        if (null !== $this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
