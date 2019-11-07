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
     * @type  \stubbles\streams\EncodingOutputStream
     */
    private $encodingOutputStream;
    /**
     * mocked input stream
     *
     * @type  \stubbles\streams\memory\MemoryOutputStream
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
    public function knowsGivenCharset()
    {
        assertThat($this->encodingOutputStream->charset(), equals('iso-8859-1'));
    }

    /**
     * @test
     */
    public function writeEncodesBytesBeforePassedToDecoratedStream()
    {
        assertThat($this->encodingOutputStream->write('hällö'), equals(5));
        assertThat($this->memory->buffer(), equals(utf8_decode('hällö')));
    }

    /**
     * @test
     */
    public function writeLineEncodesBytesBeforePassedToDecoratedStream()
    {
        assertThat($this->encodingOutputStream->writeLine('hällö'), equals(6));
        assertThat($this->memory->buffer(), equals(utf8_decode("hällö\n")));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesEncodesBytesBeforePassedToDecoratedStream()
    {
        assertThat(
                $this->encodingOutputStream->writeLines(['hällö', 'wörld']),
                equals(12)
        );
        assertThat($this->memory->buffer(), equals(utf8_decode("hällö\nwörld\n")));
    }

    /**
     * @test
     */
    public function closeClosesDecoratedOutputStream()
    {
        $outputStream = NewInstance::of(OutputStream::class);
        $encodingOutputStream = new EncodingOutputStream(
                $outputStream,
                'iso-8859-1'
        );
        $encodingOutputStream->close();
        assertTrue(verify($outputStream, 'close')->wasCalledOnce());
    }
}
