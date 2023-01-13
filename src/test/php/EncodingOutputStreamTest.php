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
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\EncodingOutputStream.
 *
 * @group  streams
 * @requires  extension iconv
 */
class EncodingOutputStreamTest extends TestCase
{
    /**
     * instance to test
     *
     * @var  \stubbles\streams\EncodingOutputStream
     */
    private $encodingOutputStream;
    /**
     * mocked input stream
     *
     * @var  \stubbles\streams\memory\MemoryOutputStream
     */
    private $memory;

    protected function setUp(): void
    {
        $this->memory = new MemoryOutputStream();
        $this->encodingOutputStream = new EncodingOutputStream(
                $this->memory,
                'iso-8859-1'
        );
    }

    /**
     * @test
     */
    public function knowsGivenCharset(): void
    {
        assertThat($this->encodingOutputStream->charset(), equals('iso-8859-1'));
    }

    /**
     * @test
     */
    public function writeEncodesBytesBeforePassedToDecoratedStream(): void
    {
        assertThat($this->encodingOutputStream->write('hällö'), equals(5));
        assertThat($this->memory->buffer(), equals(mb_convert_encoding('hällö', 'iso-8859-1')));
    }

    /**
     * @test
     */
    public function writeLineEncodesBytesBeforePassedToDecoratedStream(): void
    {
        assertThat($this->encodingOutputStream->writeLine('hällö'), equals(6));
        assertThat($this->memory->buffer(), equals(mb_convert_encoding("hällö\n", 'iso-8859-1')));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesEncodesBytesBeforePassedToDecoratedStream(): void
    {
        assertThat(
                $this->encodingOutputStream->writeLines(['hällö', 'wörld']),
                equals(12)
        );
        assertThat($this->memory->buffer(), equals(mb_convert_encoding("hällö\nwörld\n", 'iso-8859-1')));
    }

    /**
     * @test
     */
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
     * @test
     * @since 9.0.0
     */
    public function writeThrowsExceptionInIllegalCharacter(): void
    {
        $out = new MemoryOutputStream();
        $encodingOutputStream = new EncodingOutputStream($out, 'ISO-8859-1', 'CP850');
        expect(function() use($encodingOutputStream) {
            $encodingOutputStream->write("PATHOLOGIES MÉDICO-CHIRUR. ADUL. PL");
        })->throws(StreamException::class)
          ->after($out->buffer(), equals(''));
    }

    /**
     * @test
     * @since 9.0.0
     */
    public function writeLineThrowsExceptionInIllegalCharacter(): void
    {
        $out = new MemoryOutputStream();
        $encodingOutputStream = new EncodingOutputStream($out, 'ISO-8859-1', 'CP850');
        expect(function() use($encodingOutputStream) {
          $encodingOutputStream->writeLine("PATHOLOGIES MÉDICO-CHIRUR. ADUL. PL");
        })->throws(StreamException::class)
          ->after($out->buffer(), equals(''));
    }
}
