<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\ResourceOutputStream.
 *
 * @group  streams
 */
class ResourceOutputStreamTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  TestResourceOutputStream
     */
    private $resourceOutputStream;
    /**
     * the handle
     *
     * @type  resource
     */
    private $handle;
    /**
     * root directory
     *
     * @type   org\bovigo\vfs\vfsDirectory
     */
    private $root;

    protected function setUp(): void
    {
        $this->root                 = vfsStream::setup();
        $this->handle               = fopen(vfsStream::url('root/test_write.txt'), 'w');
        $this->resourceOutputStream = $this->createResourceOutputStream($this->handle);
    }

    private function createResourceOutputStream($resource): ResourceOutputStream
    {
        return new class($resource) extends ResourceOutputStream
        {
            public function __construct($handle)
            {
                $this->setHandle($handle);
            }
        };
    }

    /**
     * @test
     */
    public function invalidHandleThrowsIllegalArgumentException()
    {
        expect(function() { $this->createResourceOutputStream('invalid'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function writeToClosedStreamThrowsIllegalStateException()
    {
        $this->resourceOutputStream->close();
        expect(function() {
                $this->resourceOutputStream->write('foobarbaz');
        })
        ->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function writeLineToClosedStreamThrowsIllegalStateException()
    {
        $this->resourceOutputStream->close();
        expect(function() {
                $this->resourceOutputStream->writeLine('foobarbaz');
        })
        ->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function writeToExternalClosedStreamThrowsIOException()
    {
        fclose($this->handle);
        expect(function() {
                $this->resourceOutputStream->write('foobarbaz');
        })
        ->throws(StreamException::class);
    }

    /**
     * @test
     */
    public function writeLineToExternalClosedStreamThrowsIOException()
    {
        fclose($this->handle);
        expect(function() {
                $this->resourceOutputStream->writeLine('foobarbaz');
        })
        ->throws(StreamException::class);
    }

    /**
     * @test
     */
    public function writePassesBytesIntoStream()
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $resourceOutputStream = $this->createResourceOutputStream(
                fopen(vfsStream::url('root/test.txt'), 'w')
        );
        assertThat($resourceOutputStream->write('foobarbaz'), equals(9));
        assertThat($file->getContent(), equals('foobarbaz'));
    }

    /**
     * @test
     */
    public function writeLinePassesBytesWithLinebreakIntoStream()
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $resourceOutputStream = $this->createResourceOutputStream(
                fopen(vfsStream::url('root/test.txt'), 'w')
        );
        assertThat($resourceOutputStream->writeLine('foobarbaz'), equals(11));
        assertThat($file->getContent(), equals("foobarbaz\r\n"));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesPassesBytesWithLinebreakIntoStream()
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $resourceOutputStream = $this->createResourceOutputStream(
                fopen(vfsStream::url('root/test.txt'), 'w')
        );
        assertThat(
                $resourceOutputStream->writeLines(['foo', 'bar', 'baz']),
                equals(15)
        );
        assertThat($file->getContent(), equals("foo\r\nbar\r\nbaz\r\n"));
    }
}
