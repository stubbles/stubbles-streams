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
use InvalidArgumentException;
use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use stubbles\streams\InputStream;
use stubbles\streams\Seekable;
use stubbles\streams\StreamException;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\fail;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\streams\file\FileInputStream.
 *
 * @group streams
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
    public function constructWithString(): void
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        assertThat($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function constructWithStringFailsAndThrowsIOException(): void
    {
        expect(fn() => new FileInputStream('doesNotExist', 'r'))
            ->throws(StreamException::class)
            ->withMessage(
                'Can not open file doesNotExist with mode r: Failed to'
                . ' open stream: No such file or directory'
            );
    }

    /**
     * @test
     */
    public function constructWithResource(): void
    {
        $file = fopen(vfsStream::url('home/test.txt'), 'rb');
        if (false === $file) {
            fail('Could not open vfsStream file');
        }

        $fileInputStream = new FileInputStream($file);
        assertThat($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     * @requires extension gd
     */
    public function constructWithIllegalResource(): void
    {
        $illegalResource = imagecreate(2, 2);
        if (false === $illegalResource) {
            fail('Could not create illegal resource');
        }

        expect(fn() => new FileInputStream($illegalResource))
            ->throws(InvalidArgumentException::class);
    }

    /**
     * @test
     * @since 8.0.0
     */
    public function castFromInputStreamReturnsInputStream(): void
    {
        $inputStream = NewInstance::of(InputStream::class);
        assertThat(FileInputStream::castFrom($inputStream), isSameAs($inputStream));
    }

    /**
     * @test
     * @since 8.0.0
     */
    public function castFromStringCreatesFileInputStream(): void
    {
        assertThat(
            FileInputStream::castFrom(vfsStream::url('home/test.txt')),
            isInstanceOf(FileInputStream::class)
        );
    }

    /**
     * @test
     * @since 8.0.0
     */
    public function reportsBytesLeft(): void
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        assertThat($fileInputStream->bytesLeft(), equals(3));
    }

    /**
     * @test
     * @since 8.0.0
     */
    public function reportsBytesLeftWhenConstructedWithResource(): void
    {
        $file = fopen(vfsStream::url('home/test.txt'), 'rb');
        if (false === $file) {
            fail('Could not open vfsStream file');
        }

        $fileInputStream = new FileInputStream($file);
        assertThat($fileInputStream->bytesLeft(), equals(3));
    }

    /**
     * @test
     * @since 8.0.0
     */
    public function reportsBytesLeftForGzCompressedFilesBasedOnFilesize(): void
    {
        $fileInputStream = new FileInputStream(
            'compress.zlib://' . __DIR__ . '/../../resources/file.gz'
        );
        assertThat($fileInputStream->bytesLeft(), equals(37));
    }

    /**
     * @test
     * @since 8.0.0
     * @requires extension bz2
     */
    public function reportsBytesLeftForBzCompressedFilesBasedOnFilesize(): void
    {
        $fileInputStream = new FileInputStream(
            'compress.bzip2://' . __DIR__ . '/../../resources/file.bz2'
        );
        assertThat($fileInputStream->bytesLeft(), equals(46));
    }

    /**
     * @test
     */
    public function seek_SET(): void
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
    public function seek_CURRENT(): void
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(1, Seekable::CURRENT);
        assertThat($fileInputStream->tell(), equals(1));
        assertThat($fileInputStream->readLine(), equals('oo'));
    }

    /**
     * @test
     */
    public function seek_END(): void
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(-2, Seekable::END);
        assertThat($fileInputStream->tell(), equals(1));
        assertThat($fileInputStream->readLine(), equals('oo'));
    }

    /**
     * @test
     */
    public function seekOnClosedStreamFailsThrowsIllegalStateException(): void
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->close();
        expect(fn() => $fileInputStream->seek(3))
            ->throws(LogicException::class);
    }

    /**
     * @test
     */
    public function tellOnClosedStreamThrowsIllegalStateException(): void
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->close();
        expect(fn() => $fileInputStream->tell())
            ->throws(LogicException::class);
    }

    /**
     * @test
     * @since 8.0.0
     */
    public function tellAfterExternalCloseThrowsStreamException(): void
    {
        $fileInputStream = new class() extends FileInputStream
        {
            public function __construct()
            {
                parent::__construct(vfsStream::url('home/test.txt'));
                if (null !== $this->handle) {
                    fclose($this->handle);
                }
            }

            public function __destruct()
            {
                // intentionally empty, overwrite call to close()
            }
        };
        expect(fn() => $fileInputStream->tell())
            ->throws(LogicException::class);
    }
}
