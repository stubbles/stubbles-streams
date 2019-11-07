<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\file;
use bovigo\callmap\NewInstance;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use stubbles\streams\InputStream;
use stubbles\streams\Seekable;
use stubbles\streams\StreamException;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\streams\file\FileInputStream.
 *
 * @group  streams
 */
class FileInputStreamTest extends TestCase
{
    protected function setUp(): void
    {
        $root = vfsStream::setup('home');
        vfsStream::newFile('test.txt')->at($root)->withContent('foo');
    }

    /**
     * @test
     */
    public function constructWithString()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        assertThat($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function constructWithStringFailsAndThrowsIOException()
    {
        expect(function() { new FileInputStream('doesNotExist', 'r'); })
                ->throws(StreamException::class)
                ->withMessage(
                        'Can not open file doesNotExist with mode r: failed to'
                        . ' open stream: No such file or directory'
                );
    }

    /**
     * @test
     */
    public function constructWithResource()
    {
        $fileInputStream = new FileInputStream(fopen(vfsStream::url('home/test.txt'), 'rb'));
        assertThat($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     * @requires  extension  gd
     */
    public function constructWithIllegalResource()
    {
        expect(function() { new FileInputStream(imagecreate(2, 2)); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function castFromInputStreamReturnsInputStream()
    {
        $inputStream = NewInstance::of(InputStream::class);
        assertThat(FileInputStream::castFrom($inputStream), isSameAs($inputStream));
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function castFromStringCreatesFileInputStream()
    {
        assertThat(
                FileInputStream::castFrom(vfsStream::url('home/test.txt')),
                isInstanceOf(FileInputStream::class)
        );
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function castFromAnythingElseThrowsInvalidArgumentException()
    {
        expect(function() { FileInputStream::castFrom(404); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function reportsBytesLeft()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        assertThat($fileInputStream->bytesLeft(), equals(3));
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function reportsBytesLeftWhenConstructedWithResource()
    {
        $fileInputStream = new FileInputStream(fopen(vfsStream::url('home/test.txt'), 'rb'));
        assertThat($fileInputStream->bytesLeft(), equals(3));
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function reportsBytesLeftForGzCompressedFilesBasedOnFilesize()
    {
        $fileInputStream = new FileInputStream('compress.zlib://' . __DIR__ . '/../../resources/file.gz');
        assertThat($fileInputStream->bytesLeft(), equals(37));
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function reportsBytesLeftForBzCompressedFilesBasedOnFilesize()
    {
        $fileInputStream = new FileInputStream('compress.bzip2://' . __DIR__ . '/../../resources/file.bz2');
        assertThat($fileInputStream->bytesLeft(), equals(46));
    }

    /**
     * @test
     */
    public function seek_SET()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        assertThat($fileInputStream->tell(), equals(0));
        $fileInputStream->seek(2);
        assertThat($fileInputStream->tell(), equals(2));
        assertThat($fileInputStream->readLine(), equals('o'));
        $fileInputStream->seek(0, Seekable::SET);
        assertThat($fileInputStream->tell(), equals(0));
        assertThat($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function seek_CURRENT()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(1, Seekable::CURRENT);
        assertThat($fileInputStream->tell(), equals(1));
        assertThat($fileInputStream->readLine(), equals('oo'));
    }

    /**
     * @test
     */
    public function seek_END()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(-2, Seekable::END);
        assertThat($fileInputStream->tell(), equals(1));
        assertThat($fileInputStream->readLine(), equals('oo'));
    }

    /**
     * @test
     */
    public function seekOnClosedStreamFailsThrowsIllegalStateException()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->close();
        expect(function() use ($fileInputStream) { $fileInputStream->seek(3); })
                ->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function tellOnClosedStreamThrowsIllegalStateException()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->close();
        expect(function() use ($fileInputStream) { $fileInputStream->tell(); })
                ->throws(\LogicException::class);
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function tellAfterExternalCloseThrowsStreamException()
    {
        $fileInputStream = new class() extends FileInputStream
        {
            public function __construct()
            {
                parent::__construct(vfsStream::url('home/test.txt'));
                fclose($this->handle);
            }

            public function __destruct()
            {
                // intentionally empty, overwrite call to close()
            }
        };
        expect(function() use ($fileInputStream) { $fileInputStream->tell(); })
                ->throws(StreamException::class);
    }
}
