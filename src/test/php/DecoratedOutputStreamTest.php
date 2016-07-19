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
namespace stubbles\streams;
use bovigo\callmap\NewInstance;
use stubbles\streams\memory\MemoryOutputStream;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\DecoratedOutputStream.
 *
 * @group streams
 */
class DecoratedOutputStreamTest extends \PHPUnit_Framework_TestCase
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

    /**
     * set up test environment
     */
    public function setUp()
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
        assert($this->memory->buffer(), equals('foo'));
    }

    /**
     * @test
     */
    public function writeReturnsAmountOfDataWrittenFromDecoratedStream()
    {
        assert(
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
        assert($this->memory->buffer(), equals("foo\n"));
    }


    /**
     * @test
     */
    public function writeLineReturnsAmountOfDataWrittenFromDecoratedStream()
    {
        assert(
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
        assert($this->memory->buffer(), equals("foo\nbar\n"));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesReturnsAmountOfDataWrittenFromDecoratedStream()
    {
        assert(
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
