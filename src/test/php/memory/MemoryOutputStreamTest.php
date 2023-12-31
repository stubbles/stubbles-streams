<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\memory;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\memory\MemoryOutputStream.
 */
#[Group('streams')]
#[Group('streams_memory')]
class MemoryOutputStreamTest extends TestCase
{
    private MemoryOutputStream $memoryOutputStream;

    protected function setUp(): void
    {
        $this->memoryOutputStream = new MemoryOutputStream();
    }

    #[Test]
    public function bufferIsInitiallyEmpty(): void
    {
        assertEmptyString($this->memoryOutputStream->buffer());
    }

    /**
     * @since 4.0.0
     */
    #[Test]
    public function conversionToStringOnEmptyBufferReturnsEmptyString(): void
    {
        assertEmptyString((string) $this->memoryOutputStream);
    }

    /**
     * @since 4.0.0
     */
    #[Test]
    public function conversionToStringOnWrittenBufferReturnsBufferContents(): void
    {
        $this->memoryOutputStream->write('hello');
        assertThat((string) $this->memoryOutputStream, equals('hello'));
    }

    #[Test]
    public function writeReturnsAmountOfBytesWritten(): void
    {
        assertThat($this->memoryOutputStream->write('hello'), equals(5));
    }

    #[Test]
    public function writeWritesBytesIntoBuffer(): void
    {
        $this->memoryOutputStream->write('hello');
        assertThat($this->memoryOutputStream->buffer(), equals('hello'));
    }

    #[Test]
    public function writeLineReturnsAmountOfBytesWritten(): void
    {
        assertThat($this->memoryOutputStream->writeLine('hello'), equals(6));
    }

    #[Test]
    public function writeLineWritesBytesIntoBuffer(): void
    {
        $this->memoryOutputStream->writeLine('hello');
        assertThat($this->memoryOutputStream->buffer(), equals("hello\n"));
    }

    /**
     * @since 3.2.0
     */
    #[Test]
    public function writeLinesReturnsAmountOfBytesWritten(): void
    {
        assertThat(
            $this->memoryOutputStream->writeLines(['hello', 'world']),
            equals(12)
        );
    }

    /**
     * @since 3.2.0
     */
    #[Test]
    public function writeLinesWritesBytesIntoBuffer(): void
    {
        $this->memoryOutputStream->writeLines(['hello', 'world']);
        assertThat($this->memoryOutputStream->buffer(), equals("hello\nworld\n"));
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function closeDoesNothing(): void
    {
        $this->memoryOutputStream->close();
    }
}
