<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\streams
 */
namespace stubbles\streams\file;
use org\bovigo\vfs\vfsStream;
use stubbles\streams\Seekable;
use stubbles\streams\StreamException;

use function bovigo\assert\assert;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\file\FileInputStream.
 *
 * @group  streams
 */
class FileInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
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
        assert($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function constructWithStringFailsAndThrowsIOException()
    {
        expect(function() {
                new FileInputStream('doesNotExist', 'r');
        })
        ->throws(StreamException::class)
        ->withMessage('Can not open file doesNotExist with mode r: failed to open stream: No such file or directory');
    }

    /**
     * @test
     */
    public function constructWithResource()
    {
        $fileInputStream = new FileInputStream(fopen(vfsStream::url('home/test.txt'), 'rb'));
        assert($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function constructWithIllegalResource()
    {
        if (extension_loaded('gd') === false) {
            $this->markTestSkipped('No known extension with other resource type available.');
        }

        expect(function() {
                new FileInputStream(imagecreate(2, 2));
        })
        ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function constructWithIllegalArgument()
    {
        expect(function() {
                new FileInputStream(0);
        })
        ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function seek_SET()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        assert($fileInputStream->tell(), equals(0));
        $fileInputStream->seek(2);
        assert($fileInputStream->tell(), equals(2));
        assert($fileInputStream->readLine(), equals('o'));
        $fileInputStream->seek(0, Seekable::SET);
        assert($fileInputStream->tell(), equals(0));
        assert($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function seek_CURRENT()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(1, Seekable::CURRENT);
        assert($fileInputStream->tell(), equals(1));
        assert($fileInputStream->readLine(), equals('oo'));
    }

    /**
     * @test
     */
    public function seek_END()
    {
        $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
        $fileInputStream->seek(-2, Seekable::END);
        assert($fileInputStream->tell(), equals(1));
        assert($fileInputStream->readLine(), equals('oo'));
    }

    /**
     * @test
     */
    public function seekOnClosedStreamFailsThrowsIllegalStateException()
    {
        expect(function() {
                $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
                $fileInputStream->close();
                $fileInputStream->seek(3);
        })
        ->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function tellOnClosedStreamThrowsIllegalStateException()
    {
        expect(function() {
                $fileInputStream = new FileInputStream(vfsStream::url('home/test.txt'));
                $fileInputStream->close();
                $fileInputStream->tell();
        })
        ->throws(\LogicException::class);
    }
}
