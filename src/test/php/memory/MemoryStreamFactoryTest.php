<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\memory;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Test for stubbles\streams\memory\MemoryStreamFactory.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryStreamFactoryTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  MemoryStreamFactory
     */
    private $memoryStreamFactory;

    protected function setUp(): void
    {
        $this->memoryStreamFactory = new MemoryStreamFactory();
    }

    /**
     * @test
     */
    public function createInputStream()
    {
        $memoryInputStream = $this->memoryStreamFactory->createInputStream('buffer');
        assertThat($memoryInputStream, isInstanceOf(MemoryInputStream::class));
    }

    /**
     * @test
     */
    public function createInputStreamUsesGivenStringAsStreamContent()
    {
        $memoryInputStream = $this->memoryStreamFactory->createInputStream('buffer');
        assertThat($memoryInputStream->readLine(), equals('buffer'));
    }

    /**
     * @test
     */
    public function createOutputStream()
    {
        assertThat(
                $this->memoryStreamFactory->createOutputStream('buffer'),
                isInstanceOf(MemoryOutputStream::class)
        );
    }
}
