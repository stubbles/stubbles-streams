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
     * @var  MemoryOutputStream
     */
    private $memoryOutputStream;

    protected function setUp(): void
    {
        $this->memoryOutputStream = new MemoryOutputStream();
    }

    /**
     * @test
     */
    public function bufferIsInitiallyEmpty(): void
    {
        assertEmptyString($this->memoryOutputStream->buffer());
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function conversionToStringOnEmptyBufferReturnsEmptyString(): void
    {
        assertEmptyString((string) $this->memoryOutputStream);
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function conversionToStringOnWrittenBufferReturnsBufferContents(): void
    {
        $this->memoryOutputStream->write('hello');
        assertThat((string) $this->memoryOutputStream, equals('hello'));
    }

    /**
     * @test
     */
    public function writeReturnsAmountOfBytesWritten(): void
    {
        assertThat($this->memoryOutputStream->write('hello'), equals(5));
    }

    /**
     * @test
     */
    public function writeWritesBytesIntoBuffer(): void
    {
        $this->memoryOutputStream->write('hello');
        assertThat($this->memoryOutputStream->buffer(), equals('hello'));
    }

    /**
     * @test
     */
    public function writeLineReturnsAmountOfBytesWritten(): void
    {
        assertThat($this->memoryOutputStream->writeLine('hello'), equals(6));
    }

    /**
     * @test
     */
    public function writeLineWritesBytesIntoBuffer(): void
    {
        $this->memoryOutputStream->writeLine('hello');
        assertThat($this->memoryOutputStream->buffer(), equals("hello\n"));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesReturnsAmountOfBytesWritten(): void
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
    public function writeLinesWritesBytesIntoBuffer(): void
    {
        $this->memoryOutputStream->writeLines(['hello', 'world']);
        assertThat($this->memoryOutputStream->buffer(), equals("hello\nworld\n"));
    }

    /**
     * @test
     */
    public function closeDoesNothing(): void
    {
        assertNull($this->memoryOutputStream->close());
    }
}
