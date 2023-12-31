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
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\DecoratedInputStream.
 */
#[Group('streams')]
class DecoratedInputStreamTest extends TestCase
{
    private DecoratedInputStream $decoratedInputStream;
    private MemoryInputStream $memory;

    protected function setUp(): void
    {
        $this->memory = new MemoryInputStream("foo\n");
        $this->decoratedInputStream = $this->createDecoratedInputStream($this->memory);
    }

    private function createDecoratedInputStream(InputStream $inputStream): DecoratedInputStream
    {
        return new class($inputStream) extends DecoratedInputStream {};
    }

    #[Test]
    public function readCallsDecoratedStream(): void
    {
        assertThat($this->decoratedInputStream->read(), equals("foo\n"));
    }

    #[Test]
    public function readLineCallsDecoratedStream(): void
    {
        assertThat($this->decoratedInputStream->readLine(), equals('foo'));
    }

    #[Test]
    public function bytesLeftCallsDecoratedStream(): void
    {
        assertThat($this->decoratedInputStream->bytesLeft(), equals(4));
    }

    #[Test]
    public function eofCallsDecoratedStream(): void
    {
        assertFalse($this->decoratedInputStream->eof());
    }

    #[Test]
    public function closeCallsDecoratedStream(): void
    {
        $inputStream = NewInstance::of(InputStream::class);
        $decoratedInputStream = $this->createDecoratedInputStream($inputStream);
        $decoratedInputStream->close();
        assertTrue(verify($inputStream, 'close')->wasCalledOnce());
    }
}
