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
namespace stubbles\streams\memory;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Test for stubbles\streams\memory\MemoryStreamFactory.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryStreamFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  MemoryStreamFactory
     */
    private $memoryStreamFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryStreamFactory = new MemoryStreamFactory();
    }

    /**
     * @test
     */
    public function createInputStream()
    {
        $memoryInputStream = $this->memoryStreamFactory->createInputStream('buffer');
        assert($memoryInputStream, isInstanceOf(MemoryInputStream::class));
    }

    /**
     * @test
     */
    public function createInputStreamUsesGivenStringAsStreamContent()
    {
        $memoryInputStream = $this->memoryStreamFactory->createInputStream('buffer');
        assert($memoryInputStream->readLine(), equals('buffer'));
    }

    /**
     * @test
     */
    public function createOutputStream()
    {
        assert(
                $this->memoryStreamFactory->createOutputStream('buffer'),
                isInstanceOf(MemoryOutputStream::class)
        );
    }
}
