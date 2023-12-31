<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\streams\memory\MemoryOutputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\DecoratedOutputStream.
 */
#[Group('streams')]
class DecoratedOutputStreamTest extends TestCase
{
    private DecoratedOutputStream $decoratedOutputStream;
    private MemoryOutputStream $memory;

    protected function setUp(): void
    {
        $this->memory = new MemoryOutputStream();
        $this->decoratedOutputStream = $this->createDecoratedOutputStream($this->memory);
    }

    private function createDecoratedOutputStream(OutputStream $outputStream): DecoratedOutputStream
    {
        return new class($outputStream) extends DecoratedOutputStream {};
    }

    #[Test]
    public function writeCallsDecoratedStream(): void
    {
        $this->decoratedOutputStream->write('foo');
        assertThat($this->memory->buffer(), equals('foo'));
    }

    #[Test]
    public function writeReturnsAmountOfDataWrittenFromDecoratedStream(): void
    {
        assertThat(
            $this->decoratedOutputStream->write('foo'),
            equals(3)
        );
    }


    #[Test]
    public function writeLineCallsDecoratedStream(): void
    {
        $this->decoratedOutputStream->writeLine('foo');
        assertThat($this->memory->buffer(), equals("foo\n"));
    }


    #[Test]
    public function writeLineReturnsAmountOfDataWrittenFromDecoratedStream(): void
    {
        assertThat(
            $this->decoratedOutputStream->writeLine('foo'),
            equals(4)
        );
    }

    /**
     * @since 3.2.0
     */
    #[Test]
    public function writeLinesCallsDecoratedStream(): void
    {
        $this->decoratedOutputStream->writeLines(['foo', 'bar']);
        assertThat($this->memory->buffer(), equals("foo\nbar\n"));
    }

    /**
     * @since 3.2.0
     */
    #[Test]
    public function writeLinesReturnsAmountOfDataWrittenFromDecoratedStream(): void
    {
        assertThat(
            $this->decoratedOutputStream->writeLines(['foo', 'bar']),
            equals(8)
        );
    }

    #[Test]
    public function closeClosesDecoratedStream(): void
    {
        $outputStream = NewInstance::of(OutputStream::class);
        $decoratedOutputStream = $this->createDecoratedOutputStream($outputStream);
        $decoratedOutputStream->close();
        assertTrue(verify($outputStream, 'close')->wasCalledOnce());
    }
}
