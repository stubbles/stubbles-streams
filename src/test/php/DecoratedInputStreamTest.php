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
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\DecoratedInputStream.
 *
 * @group  streams
 */
class DecoratedInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\DecoratedInputStream
     */
    private $decoratedInputStream;
    /**
     * mocked input stream
     *
     * @type  \stubbles\streams\memory\MemoryInputStream
     */
    private $memory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memory = new MemoryInputStream("foo\n");
        $this->decoratedInputStream = $this->createDecoratedInputStream($this->memory);
    }

    private function createDecoratedInputStream(InputStream $inputStream): DecoratedInputStream
    {
        return new class($inputStream) extends DecoratedInputStream {};
    }

    /**
     * @test
     */
    public function readCallsDecoratedStream()
    {
        assert($this->decoratedInputStream->read(), equals("foo\n"));
    }

    /**
     * @test
     */
    public function readLineCallsDecoratedStream()
    {
        assert($this->decoratedInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function bytesLeftCallsDecoratedStream()
    {
        assert($this->decoratedInputStream->bytesLeft(), equals(4));
    }

    /**
     * @test
     */
    public function eofCallsDecoratedStream()
    {
        assertFalse($this->decoratedInputStream->eof());
    }

    /**
     * @test
     */
    public function closeCallsDecoratedStream()
    {
        $inputStream = NewInstance::of(InputStream::class);
        $decoratedInputStream = $this->createDecoratedInputStream($inputStream);
        $decoratedInputStream->close();
        verify($inputStream, 'close')->wasCalledOnce();
    }
}
