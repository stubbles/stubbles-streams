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
 * Interface for stream factories.
 */
interface StreamFactory
{
    /**
     * creates an input stream for given source
     *
     * @param mixed               $source  source to create input stream from
     * @param array<string,mixed> $options list of options for the input stream
     */
    public function createInputStream(mixed $source, array $options = []): InputStream;

    /**
     * creates an output stream for given target
     *
     * @param mixed               $target  target to create output stream for
     * @param array<string,mixed> $options list of options for the output stream
     */
    public function createOutputStream(mixed $target, array $options = []): OutputStream;
}
