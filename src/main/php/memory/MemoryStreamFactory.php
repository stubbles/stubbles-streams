<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\memory;
use stubbles\streams\StreamFactory;
/**
 * Factory for memory streams.
 */
class MemoryStreamFactory implements StreamFactory
{
    /**
     * creates an input stream for given source
     *
     * @param mixed $source source to create input stream from
     */
    public function createInputStream(mixed $source, array $options = []): MemoryInputStream
    {
        return new MemoryInputStream($source);
    }

    /**
     * creates an output stream for given target
     *
     * @param mixed $target target to create output stream for
     */
    public function createOutputStream(mixed $target, array $options = []): MemoryOutputStream
    {
        return new MemoryOutputStream();
    }
}
