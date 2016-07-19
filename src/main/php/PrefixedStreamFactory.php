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
 * Stream factory which prefixes source and target before calling another stream factory.
 */
class PrefixedStreamFactory implements StreamFactory
{
    /**
     * decorated stream factory
     *
     * @type  \stubbles\streams\StreamFactory
     */
    protected $streamFactory;
    /**
     * prefix to add for source and target before calling decorated stream factory
     *
     * @type  string
     */
    protected $prefix;

    /**
     * constructor
     *
     * @param  \stubbles\streams\StreamFactory  $streamFactory
     * @param  string                           $prefix
     */
    public function __construct(StreamFactory $streamFactory, string $prefix)
    {
        $this->streamFactory = $streamFactory;
        $this->prefix        = $prefix;
    }

    /**
     * creates an input stream for given source
     *
     * @param   mixed  $source   source to create input stream from
     * @param   array  $options  list of options for the input stream
     * @return  \stubbles\streams\InputStream
     */
    public function createInputStream($source, array $options = []): InputStream
    {
        return $this->streamFactory->createInputStream($this->prefix . $source, $options);
    }

    /**
     * creates an output stream for given target
     *
     * @param   mixed  $target   target to create output stream for
     * @param   array  $options  list of options for the output stream
     * @return  \stubbles\streams\OutputStream
     */
    public function createOutputStream($target, array $options = []): OutputStream
    {
        return $this->streamFactory->createOutputStream($this->prefix . $target, $options);
    }
}
