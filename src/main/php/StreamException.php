<?php
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
 * Thrown when reading or writing on a stream fails.
 */
class StreamException extends \Exception
{
    /**
     * constructor
     *
     * @param  string                    $message
     * @param  \stubbles\peer\Exception  $previous
     */
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
