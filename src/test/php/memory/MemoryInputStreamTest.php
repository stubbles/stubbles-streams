<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\memory;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\streams\Whence;

use function bovigo\assert\{
    assertThat,
    assertFalse,
    assertTrue,
    expect,
    predicate\equals
};
/**
 * Test for stubbles\streams\memory\MemoryInputStream.
 */
#[Group('streams')]
#[Group('streams_memory')]
class MemoryInputStreamTest extends TestCase
{
    private MemoryInputStream $memoryInputStream;

    protected function setUp(): void
    {
        $this->memoryInputStream = new MemoryInputStream("hello\nworld");
    }

    #[Test]
    public function isNotAtEofWhenBytesLeft(): void
    {
        assertFalse($this->memoryInputStream->eof());
    }

    #[Test]
    public function returnsAmountOfBytesLeft(): void
    {
        assertThat($this->memoryInputStream->bytesLeft(), equals(11));
    }

    #[Test]
    public function pointerIsAtBeginningAfterConstruction(): void
    {
        assertThat($this->memoryInputStream->tell(), equals(0));
    }

    #[Test]
    public function readReturnsBytes(): void
    {
        assertThat($this->memoryInputStream->read(), equals("hello\nworld"));
    }

    #[Test]
    public function hasReachedEofWhenEverythingWasRead(): void
    {
        $this->memoryInputStream->read();
        assertTrue($this->memoryInputStream->eof());
    }

    #[Test]
    public function hasNoBytesLeftWhenEverythingWasRead(): void
    {
        $this->memoryInputStream->read();
        assertThat($this->memoryInputStream->bytesLeft(), equals(0));
    }

    #[Test]
    public function pointerIsAtLastPositionWhenEverythingWasRead(): void
    {
        $this->memoryInputStream->read();
        assertThat($this->memoryInputStream->tell(), equals(11));
    }

    #[Test]
    public function readLineSplitsOnLineBreak(): void
    {
        assertThat($this->memoryInputStream->readLine(), equals('hello'));
    }

    #[Test]
    public function isNotAtEndWhenOneLineOfSeveralRead(): void
    {
        $this->memoryInputStream->readLine();
        assertFalse($this->memoryInputStream->eof());
    }

    #[Test]
    public function hasBytesLeftWhenOneLineOfServeralRead(): void
    {
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->bytesLeft(), equals(5));
    }

    #[Test]
    public function pointerIsAtOffsetOfNextLineWhenOneLineOfServeralRead(): void
    {
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->tell(), equals(6));
    }

    #[Test]
    public function readLineSplitsOnLineBreakForLastLine(): void
    {
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->readLine(), equals('world'));
    }

    #[Test]
    public function hasReachedEofAfterReadingLastLine(): void
    {
        $this->memoryInputStream->readLine();
        $this->memoryInputStream->readLine();
        assertTrue($this->memoryInputStream->eof());
    }

    #[Test]
    public function noyBytesLeftAfterReadingLastLine(): void
    {
        $this->memoryInputStream->readLine();
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->bytesLeft(), equals(0));
    }

    #[Test]
    public function pointerIsAtEndAfterReadingLastLine(): void
    {
        $this->memoryInputStream->readLine();
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->tell(), equals(11));
    }

    /**
     * @since 2.1.2
     */
    #[Test]
    public function readLineWithBothLineBreaks(): void
    {
        $this->memoryInputStream = new MemoryInputStream("hello\r\nworld");
        assertThat($this->memoryInputStream->readLine(), equals('hello'));
    }

    /**
     * @since 2.1.2
     */
    #[Test]
    public function readLineWithBothLineBreaksNextLine(): void
    {
        $this->memoryInputStream = new MemoryInputStream("hello\r\nworld");
        $this->memoryInputStream->readLine();
        assertThat($this->memoryInputStream->readLine(), equals('world'));
    }

    #[Test]
    #[DoesNotPerformAssertions]
    public function closeDoesNothing(): void
    {
        $this->memoryInputStream->close();
    }

    #[Test]
    public function seekCanSetAbsolutePosition(): void
    {
        $this->memoryInputStream->seek(6);
        assertThat($this->memoryInputStream->tell(), equals(6));
        assertThat($this->memoryInputStream->bytesLeft(), equals(5));
    }

    #[Test]
    public function seekCanSetPositionFromCurrentPosition(): void
    {
        $this->memoryInputStream->read(4);
        $this->memoryInputStream->seek(2, Whence::CURRENT);
        assertThat($this->memoryInputStream->tell(), equals(6));
        assertThat($this->memoryInputStream->bytesLeft(), equals(5));
    }

    #[Test]
    public function seekCanSetPositionFromEnd(): void
    {
        $this->memoryInputStream->seek(-5, Whence::END);
        assertThat($this->memoryInputStream->tell(), equals(6));
        assertThat($this->memoryInputStream->bytesLeft(), equals(5));
    }

    /**
     * @deprecated since 11.0.0, will be removed with 12.0.0
     */
    #[Test]
    public function seekThrowsIllegalArgumentExceptionForInvalidWhence(): void
    {
        expect(fn() => $this->memoryInputStream->seek(6, 66))
            ->throws(InvalidArgumentException::class);
    }
}
