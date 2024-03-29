<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\filter;
use stubbles\streams\DecoratedInputStream;
use stubbles\streams\InputStream;
/**
 * Input stream applying a filter on data read before returning to requestor.
 *
 * @api
 */
class FilteredInputStream extends DecoratedInputStream
{
    /** @var callable */
    private $predicate;

    /**
     * @param callable $predicate predicate to check if something should be passed
     */
    public function __construct(InputStream $inputStream, callable $predicate)
    {
        parent::__construct($inputStream);
        $this->predicate = $predicate;
    }

    /**
     * reads given amount of bytes
     */
    public function read(int $length = 8192): string
    {
        $isAcceptable = $this->predicate;
        while (!$this->inputStream->eof()) {
            $data = $this->inputStream->read($length);
            if ($isAcceptable($data)) {
                return $data;
            }
        }

        return '';
    }

    /**
     * reads given amount of bytes or until next line break
     */
    public function readLine(int $length = 8192): string
    {
        $isAcceptable = $this->predicate;
        while (!$this->inputStream->eof()) {
            $data = $this->inputStream->readLine($length);
            if ($isAcceptable($data)) {
                return $data;
            }
        }

        return '';
    }
}
