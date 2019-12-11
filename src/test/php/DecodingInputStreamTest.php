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
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\DecodingInputStream.
 *
 * @group  streams
 * @requires  extension iconv
 */
class DecodingInputStreamTest extends TestCase
{
    /**
     * instance to test
     *
     * @var  \stubbles\streams\DecodingInputStream
     */
    private $decodingInputStream;
    /**
     * mocked input stream
     *
     * @var  \stubbles\streams\memory\MemoryInputStream
     */
    private $memory;

    protected function setUp(): void
    {
        $this->memory = new MemoryInputStream(utf8_decode("hällö\n"));
        $this->decodingInputStream = new DecodingInputStream(
                $this->memory,
                'iso-8859-1'
        );
    }

    /**
     * @test
     */
    public function knowsGivenCharset(): void
    {
        assertThat($this->decodingInputStream->charset(), equals('iso-8859-1'));
    }

    /**
     * @test
     */
    public function readReturnsDecodedDataFromDecoratedStream(): void
    {
        assertThat($this->decodingInputStream->read(), equals("hällö\n"));
    }

    /**
     * @test
     */
    public function readLineReturnsDecodedLineFromDecoratedStream(): void
    {
        assertThat($this->decodingInputStream->readLine(), equals('hällö'));
    }

    /**
     * @test
     */
    public function bytesLeftReturnsBytesLeftFromDecoratedStream(): void
    {
        assertThat($this->decodingInputStream->bytesLeft(), equals(6));
    }

    /**
     * @test
     */
    public function eofReturnsEofFromDecoratedStream(): void
    {
        assertFalse($this->decodingInputStream->eof());
    }

    /**
     * @test
     */
    public function closeClosesDecoratedStream(): void
    {
        $inputStream = NewInstance::of(InputStream::class);
        $decodingInputStream = new DecodingInputStream($inputStream, 'iso-8859-1');
        $decodingInputStream->close();
        assertTrue(verify($inputStream, 'close')->wasCalledOnce());
    }

    /**
     * @test
     * @since 9.0.0
     */
    public function readThrowsExceptionInIllegalCharacter(): void
    {
      $decodingInputStream = new DecodingInputStream(
          new MemoryInputStream("PATHOLOGIES MÉDICO-CHIRUR. ADUL. PL"),
          'CP850',
          'ISO-8859-1'
      );
      expect(function() use($decodingInputStream) {
          $decodingInputStream->read();
      })->throws(StreamException::class);
    }

    /**
     * @test
     * @since 9.0.0
     */
    public function readLineThrowsExceptionInIllegalCharacter(): void
    {
      $decodingInputStream = new DecodingInputStream(
          new MemoryInputStream("PATHOLOGIES MÉDICO-CHIRUR. ADUL. PL"),
          'CP850',
          'ISO-8859-1'
      );
      expect(function() use($decodingInputStream) {
         $decodingInputStream->readLine();
      })->throws(StreamException::class);
    }
}
