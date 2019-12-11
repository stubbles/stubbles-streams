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
 * Output stream for writing to php://output.
 *
 * @since  5.4.0
 */
class StandardOutputStream extends ResourceOutputStream
{
    /**
     * constructor
     */
    public function __construct()
    {
        $fp = fopen('php://output', 'wb');
        if (false === $fp) {
            throw new \RuntimeException('Could not open input stream');
        }

        $this->setHandle($fp);
    }

    /**
     * closes the stream
     */
    public function close(): void
    {
        // intentionally empty
    }
}
