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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\{
    assertThat,
    assertEmptyString,
    assertFalse,
    assertTrue,
    expect,
    fail,
    predicate\equals
};
/**
 * Test for stubbles\streams\ResourceInputStream.
 */
#[Group('streams')]
class ResourceInputStreamTest extends TestCase
{
    private ResourceInputStream $resourceInputStream;
    /**  @var resource */
    private $handle;

    protected function setUp(): void
    {
        $root = vfsStream::setup();
        vfsStream::newFile('test_read.txt')
            ->withContent('foobarbaz
jjj')
            ->at($root);
        $handle = fopen(vfsStream::url('root/test_read.txt'), 'r');
        if (false === $handle) {
            fail('Could not open vfsStream url');
        }

        $this->handle              = $handle;
        $this->resourceInputStream = $this->createResourceInputStream($this->handle);
    }

    /**
     * @param resource $resource
     */
    private function createResourceInputStream($resource): ResourceInputStream
    {
        return new class($resource) extends ResourceInputStream
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
        expect(fn() => $this->createResourceInputStream('invalid'))
            ->throws(InvalidArgumentException::class);
    }

    #[Test]
    public function hasBytesLeftWhenOpenedAtStart(): void
    {
        assertThat($this->resourceInputStream->bytesLeft(), equals(13));
    }

    #[Test]
    public function isNotAtEofWhenOpenedAtStart(): void
    {
        assertFalse($this->resourceInputStream->eof());
    }

    #[Test]
    public function hasNoBytesLeftWhenEverythingRead(): void
    {
        $this->resourceInputStream->read();
        assertThat($this->resourceInputStream->bytesLeft(), equals(0));
    }

    #[Test]
    public function read(): void
    {
        assertThat($this->resourceInputStream->read(), equals("foobarbaz\njjj"));
    }

    #[Test]
    public function readBytes(): void
    {
        assertThat($this->resourceInputStream->read(6), equals('foobar'));
    }

    #[Test]
    public function hasBytesLeftWhenNotEverythingRead(): void
    {
        $this->resourceInputStream->read(6);
        assertThat($this->resourceInputStream->bytesLeft(), equals(7));
    }

    #[Test]
    public function readLine(): void
    {
        assertThat($this->resourceInputStream->readLine(), equals('foobarbaz'));
    }

    #[Test]
    public function hasReachedEofWhenEverythingRead(): void
    {
        $this->resourceInputStream->read();
        assertTrue($this->resourceInputStream->eof());
    }

    #[Test]
    public function readAfterEofReturnsEmptyString(): void
    {
        $this->resourceInputStream->read();
        assertEmptyString($this->resourceInputStream->read());
    }

    #[Test]
    public function readAfterCloseFails(): void
    {
        expect(function() {
                $this->resourceInputStream->close();
                $this->resourceInputStream->read();
        })
            ->throws(LogicException::class);
    }

    #[Test]
    public function readLineAfterCloseFails(): void
    {
        expect(function() {
                $this->resourceInputStream->close();
                $this->resourceInputStream->readLine();
        })
            ->throws(LogicException::class);
    }

    #[Test]
    public function bytesLeftAfterCloseFails(): void
    {
        expect(function() {
                $this->resourceInputStream->close();
                $this->resourceInputStream->bytesLeft();
        })
            ->throws(LogicException::class);
    }

    /**
     * @since 9.1.0
     */
    #[Test]
    public function eofAfterCloseFails(): void
    {
        expect(function() {
            $this->resourceInputStream->close();
            $this->resourceInputStream->eof();
        })
            ->throws(LogicException::class);
    }

    #[Test]
    public function readAfterCloseFromOutsite(): void
    {
        expect(function() {
                fclose($this->handle);
                $this->resourceInputStream->read();
        })
            ->throws(LogicException::class);
    }

    #[Test]
    public function readLineAfterCloseFromOutsite(): void
    {
        expect(function() {
                fclose($this->handle);
                $this->resourceInputStream->readLine();
        })
            ->throws(LogicException::class);
    }

    #[Test]
    public function bytesLeftAfterCloseFromOutsite(): void
    {
        expect(function() {
                fclose($this->handle);
                $this->resourceInputStream->bytesLeft();
        })
            ->throws(\LogicException::class);
    }
}
