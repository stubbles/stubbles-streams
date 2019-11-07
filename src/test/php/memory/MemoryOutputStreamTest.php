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
use function bovigo\assert\assertNull;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\memory\MemoryOutputStream.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryOutputStreamTest extends TestCase
{
    /**
     * the file url used in the tests
     *
     * @type  MemoryOutputStream
     */
    private $memoryOutputStream;

    protected function setUp(): void
    {
        $this->memoryOutputStream = new MemoryOutputStream();
    }

    /**
     * @test
     */
    public function bufferIsInitiallyEmpty()
    {
        assertEmptyString($this->memoryOutputStream->buffer());
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function conversionToStringOnEmptyBufferReturnsEmptyString()
    {
        assertEmptyString((string) $this->memoryOutputStream);
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function conversionToStringOnWrittenBufferReturnsBufferContents()
    {
        $this->memoryOutputStream->write('hello');
        assertThat((string) $this->memoryOutputStream, equals('hello'));
    }

    /**
     * @test
     */
    public function writeReturnsAmountOfBytesWritten()
    {
        assertThat($this->memoryOutputStream->write('hello'), equals(5));
    }

    /**
     * @test
     */
    public function writeWritesBytesIntoBuffer()
    {
        $this->memoryOutputStream->write('hello');
        assertThat($this->memoryOutputStream->buffer(), equals('hello'));
    }

    /**
     * @test
     */
    public function writeLineReturnsAmountOfBytesWritten()
    {
        assertThat($this->memoryOutputStream->writeLine('hello'), equals(6));
    }

    /**
     * @test
     */
    public function writeLineWritesBytesIntoBuffer()
    {
        $this->memoryOutputStream->writeLine('hello');
        assertThat($this->memoryOutputStream->buffer(), equals("hello\n"));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesReturnsAmountOfBytesWritten()
    {
        assertThat(
                $this->memoryOutputStream->writeLines(['hello', 'world']),
                equals(12)
        );
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesWritesBytesIntoBuffer()
    {
        $this->memoryOutputStream->writeLines(['hello', 'world']);
        assertThat($this->memoryOutputStream->buffer(), equals("hello\nworld\n"));
    }

    /**
     * @test
     */
    public function closeDoesNothing()
    {
        assertNull($this->memoryOutputStream->close());
    }
}
