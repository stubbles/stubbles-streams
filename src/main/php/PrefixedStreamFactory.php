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
 * Stream factory which prefixes source and target before calling another stream factory.
 */
class PrefixedStreamFactory implements StreamFactory
{
    /**
     * @param StreamFactory $streamFactory stream factory to decorate
     * @param string        $prefix prefix to add for source and target before calling decorated stream factory
     */
    public function __construct(private StreamFactory $streamFactory, private string $prefix) { }

    /**
     * creates an input stream for given source
     *
     * @param  mixed               $source  source to create input stream from
     * @param  array<string,mixed> $options list of options for the input stream
     */
    public function createInputStream(mixed $source, array $options = []): InputStream
    {
        return $this->streamFactory->createInputStream($this->prefix . $source, $options);
    }

    /**
     * creates an output stream for given target
     *
     * @param  mixed               $target  target to create output stream for
     * @param  array<string,mixed> $options list of options for the output stream
     */
    public function createOutputStream(mixed $target, array $options = []): OutputStream
    {
        return $this->streamFactory->createOutputStream($this->prefix . $target, $options);
    }
}
