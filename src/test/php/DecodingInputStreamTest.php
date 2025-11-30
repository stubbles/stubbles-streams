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
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\DecodingInputStream.
 */
#[Group('streams')]
#[RequiresPhpExtension('iconv')]
class DecodingInputStreamTest extends TestCase
{
    private DecodingInputStream $decodingInputStream;
    private MemoryInputStream $memory;

    protected function setUp(): void
    {
        $this->memory = new MemoryInputStream(mb_convert_encoding("hällö\n", 'iso-8859-1'));
        $this->decodingInputStream = new DecodingInputStream(
            $this->memory,
            'iso-8859-1'
        );
    }

    #[Test]
    public function knowsGivenCharset(): void
    {
        assertThat($this->decodingInputStream->charset(), equals('iso-8859-1'));
    }

    #[Test]
    public function readReturnsDecodedDataFromDecoratedStream(): void
    {
        assertThat($this->decodingInputStream->read(), equals("hällö\n"));
    }

    #[Test]
    public function readLineReturnsDecodedLineFromDecoratedStream(): void
    {
        assertThat($this->decodingInputStream->readLine(), equals('hällö'));
    }

    #[Test]
    public function bytesLeftReturnsBytesLeftFromDecoratedStream(): void
    {
        assertThat($this->decodingInputStream->bytesLeft(), equals(6));
    }

    #[Test]
    public function eofReturnsEofFromDecoratedStream(): void
    {
        assertFalse($this->decodingInputStream->eof());
    }

    #[Test]
    public function closeClosesDecoratedStream(): void
    {
        $inputStream = NewInstance::of(InputStream::class);
        $decodingInputStream = new DecodingInputStream($inputStream, 'iso-8859-1');
        $decodingInputStream->close();
        assertTrue(verify($inputStream, 'close')->wasCalledOnce());
    }

    /**
     * @since 9.0.0
     */
    #[Test]
    #[Group('encoding_failure')]
    public function readThrowsExceptionInIllegalCharacter(): void
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            $this->markTestSkipped('Conversion on macOS yields an invalid resulting string and not an error.');
        }

        $decodingInputStream = new DecodingInputStream(
            new MemoryInputStream("PATHOLOGIES MÉDICO-CHIRUR. ADUL. PL"),
            'CP850',
            'ISO-8859-1'
        );
        expect(fn() => $decodingInputStream->read())
            ->throws(StreamException::class);
    }

    /**
     * @since 9.0.0
     */
    #[Test]
    #[Group('encoding_failure')]
    public function readLineThrowsExceptionInIllegalCharacter(): void
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            $this->markTestSkipped('Conversion on macOS yields an invalid resulting string and not an error.');
        }

        $decodingInputStream = new DecodingInputStream(
            new MemoryInputStream("PATHOLOGIES MÉDICO-CHIRUR. ADUL. PL"),
            'CP850',
            'ISO-8859-1'
        );
        expect(fn() => $decodingInputStream->readLine())
            ->throws(StreamException::class);
    }
}
