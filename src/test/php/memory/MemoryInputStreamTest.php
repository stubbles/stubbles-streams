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
use stubbles\streams\Seekable;

use function bovigo\assert\{
    assertThat,
    assertFalse,
    assertNull,
    assertTrue,
    expect,
    predicate\equals
};
/**
 * Test for stubbles\streams\memory\MemoryInputStream.
 *
 * @group  streams
 * @group  streams_memory
 */
class MemoryInputStreamTest extends TestCase
{
    /**
     * the file url used in the tests
     *
     * @type  MemoryInputStream
     */
    private $memoryInputStream;

    protected function setUp(): void
    {
        $this->memoryInputStream = new MemoryInputStream("hello\nworld");
    }

    /**
     * @test
     */
    public function isNotAtEofWhenBytesLeft()
    {
        assertFalse($this->memoryInputStream->eof());
    }

    /**
     * @test
     */
    public function returnsAmountOfBytesLeft()
    {
        assertThat($this->memoryInputStream->bytesLeft(), equals(11));
    }

    /**
     * @test
     */
    public function pointerIsAtBeginningAfterConstruction()
    {
        assertThat($this->memoryInputStream->tell(), equals(0));
    }

    /**
     * @test
     */
    public function readReturnsBytes()
    {
        assertThat($this->memoryInputStream->read(), equals("hello\nworld"));
    }

    /**
     * @test
     */
    public function hasReachedEofWhenEverythingWasRead()
    {
        $this->memoryInputStream->read();
        assertTrue($this->memoryInputStream->eof());
    }

    /**
     * @test
     */
    public function hasNoBytesLeftWhenEverythingWasRead()
    {
        $this->memoryInputStream->read();
        assertThat($this->memoryInputStream->bytesLeft(), equals(0));
    }

    /**
     * @test
     */
    public function pointerIsAtLastPositionWhenEverythingWasRead()
    {
        $this->memoryInputStream->read();
        assertThat($this->memoryInputStream->tell(), equals(11));
    }

    /**
     * @test
     */
    public function readLineSplitsOnLineBreak()
    {
        assertThat($this->memoryInputStream->readLine(), equals('hello'));
    }

    /**
     * @test
     */
    public function isNotAtEndWhenOneLineOfSeveralRead()
    {
        $this->memoryInputStream->readLine();
        assertFalse($this->memoryInputStream->eof());
    }

    /**
     * @test
     */
    public function hasBytesLeftWhenOneLineOfServeralRead()
    {
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->bytesLeft(), equals(5));
    }

    /**
     * @test
     */
    public function pointerIsAtOffsetOfNextLineWhenOneLineOfServeralRead()
    {
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->tell(), equals(6));
    }

    /**
     * @test
     */
    public function readLineSplitsOnLineBreakForLastLine()
    {
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->readLine(), equals('world'));
    }

    /**
     * @test
     */
    public function hasReachedEofAfterReadingLastLine()
    {
        $this->memoryInputStream->readLine();
        $this->memoryInputStream->readLine();
        assertTrue($this->memoryInputStream->eof());
    }

    /**
     * @test
     */
    public function noyBytesLeftAfterReadingLastLine()
    {
        $this->memoryInputStream->readLine();
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->bytesLeft(), equals(0));
    }

    /**
     * @test
     */
    public function pointerIsAtEndAfterReadingLastLine()
    {
        $this->memoryInputStream->readLine();
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->tell(), equals(11));
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function readLineWithBothLineBreaks()
    {
        $this->memoryInputStream = new MemoryInputStream("hello\r\nworld");
        assertThat($this->memoryInputStream->readLine(), equals('hello'));
    }

    /**
     * @since  2.1.2
     * @test
     */
    public function readLineWithBothLineBreaksNextLine()
    {
        $this->memoryInputStream = new MemoryInputStream("hello\r\nworld");
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->readLine(), equals('world'));
    }

    /**
     * @test
     */
    public function closeDoesNothing()
    {
        assertNull($this->memoryInputStream->close());
    }

    /**
     * @test
     */
    public function seekCanSetAbsolutePosition()
    {
        $this->memoryInputStream->seek(6);
        assertThat($this->memoryInputStream->tell(), equals(6));
        assertThat($this->memoryInputStream->bytesLeft(), equals(5));
    }

    /**
     * seek() sets position of of buffer
     *
     * @test
     */
    public function seekCanSetPositionFromCurrentPosition()
    {
        $this->memoryInputStream->read(4);
        $this->memoryInputStream->seek(2, Seekable::CURRENT);
        assertThat($this->memoryInputStream->tell(), equals(6));
        assertThat($this->memoryInputStream->bytesLeft(), equals(5));
    }

    /**
     * @test
     */
    public function seekCanSetPositionFromEnd()
    {
        $this->memoryInputStream->seek(-5, Seekable::END);
        assertThat($this->memoryInputStream->tell(), equals(6));
        assertThat($this->memoryInputStream->bytesLeft(), equals(5));
    }

    /**
     * @test
     */
    public function seekThrowsIllegalArgumentExceptionForInvalidWhence()
    {
        expect(function() { $this->memoryInputStream->seek(6, 66); })
                ->throws(\InvalidArgumentException::class);
    }
}
