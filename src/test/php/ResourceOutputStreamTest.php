<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;

use InvalidArgumentException;
use LogicException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\fail;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\ResourceOutputStream.
 */
#[Group('streams')]
class ResourceOutputStreamTest extends TestCase
{
    private ResourceOutputStream $resourceOutputStream;
    /** @var resource */
    private $handle;
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup();
        $handle = fopen(vfsStream::url('root/test_write.txt'), 'w');
        if (false === $handle) {
            fail('Could not open vfsStream url');
        }

        $this->handle               = $handle;
        $this->resourceOutputStream = $this->createResourceOutputStream($this->handle);
    }

    /**
     * @param resource $resource
     */
    private function createResourceOutputStream($resource): ResourceOutputStream
    {
        return new class($resource) extends ResourceOutputStream
        {
            /**
             * @param resource $handle
             */
            public function __construct($handle)
            {
                $this->setHandle($handle);
            }
        };
    }

    #[Test]
    public function invalidHandleThrowsIllegalArgumentException(): void
    {
        expect(fn() => $this->createResourceOutputStream('invalid'))
            ->throws(InvalidArgumentException::class);
    }

    #[Test]
    public function writeToClosedStreamThrowsIllegalStateException(): void
    {
        $this->resourceOutputStream->close();
        expect(fn() => $this->resourceOutputStream->write('foobarbaz'))
            ->throws(LogicException::class);
    }

    #[Test]
    public function writeLineToClosedStreamThrowsIllegalStateException(): void
    {
        $this->resourceOutputStream->close();
        expect(fn() => $this->resourceOutputStream->writeLine('foobarbaz'))
            ->throws(LogicException::class);
    }

    #[Test]
    public function writeToExternalClosedStreamThrowsIOException(): void
    {
        fclose($this->handle);
        expect(fn() => $this->resourceOutputStream->write('foobarbaz'))
            ->throws(LogicException::class);
    }

    #[Test]
    public function writeLineToExternalClosedStreamThrowsIOException(): void
    {
        fclose($this->handle);
        expect(fn() => $this->resourceOutputStream->writeLine('foobarbaz'))
            ->throws(LogicException::class);
    }

    #[Test]
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

    #[Test]
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
     * @since 3.2.0
     */
    #[Test]
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
