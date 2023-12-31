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
use stubbles\streams\memory\MemoryOutputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\EncodingOutputStream.
 */
#[Group('streams')]
#[RequiresPhpExtension('iconv')]
class EncodingOutputStreamTest extends TestCase
{
    private EncodingOutputStream $encodingOutputStream;
    private MemoryOutputStream $memory;

    protected function setUp(): void
    {
        $this->memory = new MemoryOutputStream();
        $this->encodingOutputStream = new EncodingOutputStream(
            $this->memory,
            'iso-8859-1'
        );
    }

    #[Test]
    public function knowsGivenCharset(): void
    {
        assertThat($this->encodingOutputStream->charset(), equals('iso-8859-1'));
    }

    #[Test]
    public function writeEncodesBytesBeforePassedToDecoratedStream(): void
    {
        assertThat($this->encodingOutputStream->write('hällö'), equals(5));
        assertThat($this->memory->buffer(), equals(mb_convert_encoding('hällö', 'iso-8859-1')));
    }

    #[Test]
    public function writeLineEncodesBytesBeforePassedToDecoratedStream(): void
    {
        assertThat($this->encodingOutputStream->writeLine('hällö'), equals(6));
        assertThat($this->memory->buffer(), equals(mb_convert_encoding("hällö\n", 'iso-8859-1')));
    }

    /**
     * @since 3.2.0
     */
    #[Test]
    public function writeLinesEncodesBytesBeforePassedToDecoratedStream(): void
    {
        assertThat(
            $this->encodingOutputStream->writeLines(['hällö', 'wörld']),
            equals(12)
        );
        assertThat(
            $this->memory->buffer(),
            equals(mb_convert_encoding("hällö\nwörld\n", 'iso-8859-1'))
        );
    }

    #[Test]
    public function closeClosesDecoratedOutputStream(): void
    {
        $outputStream = NewInstance::of(OutputStream::class);
        $encodingOutputStream = new EncodingOutputStream(
            $outputStream,
            'iso-8859-1'
        );
        $encodingOutputStream->close();
        assertTrue(verify($outputStream, 'close')->wasCalledOnce());
    }

    /**
     * @since 9.0.0
     */
    #[Test]
    public function writeThrowsExceptionInIllegalCharacter(): void
    {
        $out = new MemoryOutputStream();
        $encodingOutputStream = new EncodingOutputStream($out, 'ISO-8859-1', 'CP850');
        expect(fn() => $encodingOutputStream->write("PATHOLOGIES MÉDICO-CHIRUR. ADUL. PL"))
            ->throws(StreamException::class)
            ->after($out->buffer(), equals(''));
    }

    /**
     * @since 9.0.0
     */
    #[Test]
    public function writeLineThrowsExceptionInIllegalCharacter(): void
    {
        $out = new MemoryOutputStream();
        $encodingOutputStream = new EncodingOutputStream($out, 'ISO-8859-1', 'CP850');
        expect(fn() => $encodingOutputStream->writeLine("PATHOLOGIES MÉDICO-CHIRUR. ADUL. PL"))
            ->throws(StreamException::class)
            ->after($out->buffer(), equals(''));
    }
}
