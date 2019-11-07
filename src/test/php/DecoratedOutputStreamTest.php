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
use PHPUnit\Framework\TestCase;
use stubbles\streams\memory\MemoryOutputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\DecoratedOutputStream.
 *
 * @group streams
 */
class DecoratedOutputStreamTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\DecoratedOutputStream
     */
    private $decoratedOutputStream;
    /**
     * mocked input stream
     *
     * @type  \stubbles\streams\memory\MemoryOutputStream
     */
    private $memory;

    protected function setUp(): void
    {
        $this->memory = new MemoryOutputStream();
        $this->decoratedOutputStream = $this->createDecoratedOutputStream($this->memory);
    }

    private function createDecoratedOutputStream(OutputStream $outputStream): DecoratedOutputStream
    {
        return new class($outputStream) extends DecoratedOutputStream {};
    }

    /**
     * @test
     */
    public function writeCallsDecoratedStream()
    {
        $this->decoratedOutputStream->write('foo');
        assertThat($this->memory->buffer(), equals('foo'));
    }

    /**
     * @test
     */
    public function writeReturnsAmountOfDataWrittenFromDecoratedStream()
    {
        assertThat(
                $this->decoratedOutputStream->write('foo'),
                equals(3)
        );
    }


    /**
     * @test
     */
    public function writeLineCallsDecoratedStream()
    {
        $this->decoratedOutputStream->writeLine('foo');
        assertThat($this->memory->buffer(), equals("foo\n"));
    }


    /**
     * @test
     */
    public function writeLineReturnsAmountOfDataWrittenFromDecoratedStream()
    {
        assertThat(
                $this->decoratedOutputStream->writeLine('foo'),
                equals(4)
        );
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesCallsDecoratedStream()
    {
        $this->decoratedOutputStream->writeLines(['foo', 'bar']);
        assertThat($this->memory->buffer(), equals("foo\nbar\n"));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesReturnsAmountOfDataWrittenFromDecoratedStream()
    {
        assertThat(
                $this->decoratedOutputStream->writeLines(['foo', 'bar']),
                equals(8)
        );
    }

    /**
     * @test
     */
    public function closeClosesDecoratedStream()
    {
        $outputStream = NewInstance::of(OutputStream::class);
        $decoratedOutputStream = $this->createDecoratedOutputStream(
                $outputStream
        );
        $decoratedOutputStream->close();
        verify($outputStream, 'close')->wasCalledOnce();
    }
}
