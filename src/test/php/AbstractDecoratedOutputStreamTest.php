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
 * Test for stubbles\streams\AbstractDecoratedOutputStream.
 *
 * @group streams
 */
class AbstractDecoratedOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\AbstractDecoratedOutputStream
     */
    private $abstractDecoratedOutputStream;
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
        $this->abstractDecoratedOutputStream = $this->createDecoratedOutputStream($this->memory);
    }

    private function createDecoratedOutputStream(OutputStream $outputStream): AbstractDecoratedOutputStream
    {
        return new class($outputStream) extends AbstractDecoratedOutputStream {};
    }

    /**
     * @test
     */
    public function writeCallsDecoratedStream()
    {
        assert(
                $this->abstractDecoratedOutputStream->write('foo'),
                equals(3)
        );
        assert($this->memory->buffer(), equals('foo'));
    }

    /**
     * @test
     */
    public function writeLineCallsDecoratedStream()
    {
        assert(
                $this->abstractDecoratedOutputStream->writeLine('foo'),
                equals(4)
        );
        assert($this->memory->buffer(), equals("foo\n"));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesCallsDecoratedStream()
    {
        assert(
                $this->abstractDecoratedOutputStream->writeLines(['foo', 'bar']),
                equals(8)
        );
        assert($this->memory->buffer(), equals("foo\nbar\n"));
    }

    /**
     * @test
     */
    public function closeClosesDecoratedStream()
    {
        $outputStream = NewInstance::of(OutputStream::class);
        $abstractDecoratedOutputStream = $this->createDecoratedOutputStream(
                $outputStream
        );
        $abstractDecoratedOutputStream->close();
        verify($outputStream, 'close')->wasCalledOnce();
    }
}
