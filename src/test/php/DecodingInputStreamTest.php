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
     * @type  \stubbles\streams\DecodingInputStream
     */
    private $decodingInputStream;
    /**
     * mocked input stream
     *
     * @type  \stubbles\streams\memory\MemoryInputStream
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
    public function knowsGivenCharset()
    {
        assertThat($this->decodingInputStream->charset(), equals('iso-8859-1'));
    }

    /**
     * @test
     */
    public function readReturnsDecodedDataFromDecoratedStream()
    {
        assertThat($this->decodingInputStream->read(), equals("hällö\n"));
    }

    /**
     * @test
     */
    public function readLineReturnsDecodedLineFromDecoratedStream()
    {
        assertThat($this->decodingInputStream->readLine(), equals('hällö'));
    }

    /**
     * @test
     */
    public function bytesLeftReturnsBytesLeftFromDecoratedStream()
    {
        assertThat($this->decodingInputStream->bytesLeft(), equals(6));
    }

    /**
     * @test
     */
    public function eofReturnsEofFromDecoratedStream()
    {
        assertFalse($this->decodingInputStream->eof());
    }

    /**
     * @test
     */
    public function closeClosesDecoratedStream()
    {
        $inputStream = NewInstance::of(InputStream::class);
        $decodingInputStream = new DecodingInputStream($inputStream, 'iso-8859-1');
        $decodingInputStream->close();
        assertTrue(verify($inputStream, 'close')->wasCalledOnce());
    }
}
