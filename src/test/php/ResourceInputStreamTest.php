<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\streams
 */
namespace stubbles\streams;
use org\bovigo\vfs\vfsStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Helper class for the test.
 */
class TestResourceInputStream extends ResourceInputStream
{
    /**
     * constructor
     *
     * @param   resource  $handle
     */
    public function __construct($handle)
    {
        $this->setHandle($handle);
    }
}
/**
 * Test for stubbles\streams\ResourceInputStream.
 *
 * @group  streams
 */
class ResourceInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  TestResourceInputStream
     */
    protected $resourceInputStream;
    /**
     * the handle
     *
     * @type  resource
     */
    protected $handle;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $root = vfsStream::setup();
        vfsStream::newFile('test_read.txt')
                 ->withContent('foobarbaz
jjj')
                 ->at($root);
        $this->handle              = fopen(vfsStream::url('root/test_read.txt'), 'r');
        $this->resourceInputStream = new TestResourceInputStream($this->handle);
    }

    /**
     * @test
     */
    public function invalidHandleThrowsIllegalArgumentException()
    {
        expect(function() {
                new TestResourceInputStream('invalid');
        })
        ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function hasBytesLeftWhenOpenedAtStart()
    {
        assert($this->resourceInputStream->bytesLeft(), equals(13));
    }

    /**
     * @test
     */
    public function isNotAtEofWhenOpenedAtStart()
    {
        assertFalse($this->resourceInputStream->eof());
    }

    /**
     * @test
     */
    public function hasNoBytesLeftWhenEverythingRead()
    {
        $this->resourceInputStream->read();
        assert($this->resourceInputStream->bytesLeft(), equals(0));
    }

    /**
     * @test
     */
    public function read()
    {
        assert($this->resourceInputStream->read(), equals("foobarbaz\njjj"));
    }

    /**
     * @test
     */
    public function readBytes()
    {
        assert($this->resourceInputStream->read(6), equals('foobar'));
    }

    /**
     * @test
     */
    public function hasBytesLeftWhenNotEverythingRead()
    {
        $this->resourceInputStream->read(6);
        assert($this->resourceInputStream->bytesLeft(), equals(7));
    }

    /**
     * @test
     */
    public function readLine()
    {
        assert($this->resourceInputStream->readLine(), equals('foobarbaz'));
    }

    /**
     * @test
     */
    public function hasReachedEofWhenEverythingRead()
    {
        $this->resourceInputStream->read();
        assertTrue($this->resourceInputStream->eof());
    }

    /**
     * @test
     */
    public function readAfterEofReturnsEmptyString()
    {
        $this->resourceInputStream->read();
        assertEmptyString($this->resourceInputStream->read());
    }

    /**
     * @test
     */
    public function readAfterCloseFails()
    {
        expect(function() {
                $this->resourceInputStream->close();
                $this->resourceInputStream->read();
        })
        ->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function readLineAfterCloseFails()
    {
        expect(function() {
                $this->resourceInputStream->close();
                $this->resourceInputStream->readLine();
        })
        ->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function bytesLeftAfterCloseFails()
    {
        expect(function() {
                $this->resourceInputStream->close();
                $this->resourceInputStream->bytesLeft();
        })
        ->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function readAfterCloseFromOutsite()
    {
        expect(function() {
                fclose($this->handle);
                $this->resourceInputStream->read();
        })
        ->throws(StreamException::class);
    }

    /**
     * @test
     */
    public function readLineAfterCloseFromOutsite()
    {
        expect(function() {
                fclose($this->handle);
                $this->resourceInputStream->readLine();
        })
        ->throws(StreamException::class);
    }

    /**
     * @test
     */
    public function bytesLeftAfterCloseFromOutsite()
    {
        expect(function() {
                fclose($this->handle);
                $this->resourceInputStream->bytesLeft();
        })
        ->throws(\LogicException::class);
    }
}
