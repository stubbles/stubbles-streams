<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;

use LogicException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\fail;
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
     * @var  ResourceOutputStream
     */
    private $resourceOutputStream;
    /**
     * the handle
     *
     * @var  resource
     */
    private $handle;
    /**
     * root directory
     *
     * @var  \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    protected function setUp(): void
    {
        $this->root                 = vfsStream::setup();
        $handle = fopen(vfsStream::url('root/test_write.txt'), 'w');
        if (false === $handle) {
            fail('Could not open vfsStream url');
        }

        $this->handle               = $handle;
        $this->resourceOutputStream = $this->createResourceOutputStream($this->handle);
    }

    /**
     * @param   resource  $resource
     * @return  ResourceOutputStream
     */
    private function createResourceOutputStream($resource): ResourceOutputStream
    {
        return new class($resource) extends ResourceOutputStream
        {
            /**
             * @param  resource  $handle
             */
            public function __construct($handle)
            {
                $this->setHandle($handle);
            }
        };
    }

    /**
     * @test
     */
    public function invalidHandleThrowsIllegalArgumentException(): void
    {
        expect(function() { $this->createResourceOutputStream('invalid'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function writeToClosedStreamThrowsIllegalStateException(): void
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
    public function writeLineToClosedStreamThrowsIllegalStateException(): void
    {
        $this->resourceOutputStream->close();
        expect(function() {
                $this->resourceOutputStream->writeLine('foobarbaz');
        })
            ->throws(LogicException::class);
    }

    /**
     * @test
     */
    public function writeToExternalClosedStreamThrowsIOException(): void
    {
        fclose($this->handle);
        expect(function() {
                $this->resourceOutputStream->write('foobarbaz');
        })
            ->throws(LogicException::class);
    }

    /**
     * @test
     */
    public function writeLineToExternalClosedStreamThrowsIOException(): void
    {
        fclose($this->handle);
        expect(function() {
                $this->resourceOutputStream->writeLine('foobarbaz');
        })
            ->throws(LogicException::class);
    }

    /**
     * @test
     */
    public function writePassesBytesIntoStream(): void
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $res  = fopen(vfsStream::url('root/test.txt'), 'w');
        if (false === $res) {
            fail('Could not open vfsStream url');
        }

        $resourceOutputStream = $this->createResourceOutputStream($res);
        assertThat($resourceOutputStream->write('foobarbaz'), equals(9));
        assertThat($file->getContent(), equals('foobarbaz'));
    }

    /**
     * @test
     */
    public function writeLinePassesBytesWithLinebreakIntoStream(): void
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $res  = fopen(vfsStream::url('root/test.txt'), 'w');
        if (false === $res) {
            fail('Could not open vfsStream url');
        }

        $resourceOutputStream = $this->createResourceOutputStream($res);
        assertThat($resourceOutputStream->writeLine('foobarbaz'), equals(11));
        assertThat($file->getContent(), equals("foobarbaz\r\n"));
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesPassesBytesWithLinebreakIntoStream(): void
    {
        $file = vfsStream::newFile('test.txt')->at($this->root);
        $res  = fopen(vfsStream::url('root/test.txt'), 'w');
        if (false === $res) {
            fail('Could not open vfsStream url');
        }

        $resourceOutputStream = $this->createResourceOutputStream($res);
        assertThat(
                $resourceOutputStream->writeLines(['foo', 'bar', 'baz']),
                equals(15)
        );
        assertThat($file->getContent(), equals("foo\r\nbar\r\nbaz\r\n"));
    }
}
