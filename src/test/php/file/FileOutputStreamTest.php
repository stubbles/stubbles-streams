<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\file;

use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use PHPUnit\Framework\TestCase;
use stubbles\streams\StreamException;

use function bovigo\assert\{
    assertThat,
    assertFalse,
    assertTrue,
    expect,
    fail,
    predicate\equals
};
/**
 * Test for stubbles\streams\file\FileOutputStream.
 */
#[Group('streams')]
#[Group('streams_file')]
class FileOutputStreamTest extends TestCase
{
    private string $fileUrl;

    protected function setUp(): void
    {
        vfsStream::setup('home');
        $this->fileUrl = vfsStream::url('home/test.txt');
    }

    #[Test]
    public function constructWithStringCreatesFile(): void
    {
        new FileOutputStream($this->fileUrl);
        assertTrue(file_exists($this->fileUrl));
    }

    #[Test]
    public function constructWithStringDelayedDoesNotCreateFile(): void
    {
        new FileOutputStream($this->fileUrl, 'wb', true);
        assertFalse(file_exists($this->fileUrl));
    }

    #[Test]
    public function constructWithString(): void
    {
        $fileOutputStream = new FileOutputStream($this->fileUrl);
        $fileOutputStream->write('foo');
        assertThat(file_get_contents($this->fileUrl), equals('foo'));
    }

    #[Test]
    public function constructWithStringDelayedCreatesFileOnWrite(): void
    {
        $fileOutputStream = new FileOutputStream($this->fileUrl, 'wb', true);
        $fileOutputStream->write('foo');
        assertTrue(file_exists($this->fileUrl));
    }

    #[Test]
    #[WithoutErrorHandler]
    public function constructWithStringFailsAndThrowsIOException(): void
    {
        vfsStream::newFile('test.txt', 0000)->at(vfsStream::setup());
        expect(fn() => new FileOutputStream($this->fileUrl, 'r'))
            ->throws(StreamException::class)
            ->withMessage(
                'Can not open file vfs://home/test.txt with mode r:'
                . ' Failed to open stream: "org\bovigo\vfs\vfsStreamWrapper::stream_open"'
                . ' call failed'
            );
    }

    #[Test]
    public function constructWithResource(): void
    {
        $file = fopen($this->fileUrl, 'wb');
        if (false === $file) {
            fail('Could not open vfsStream file');
        }

        $fileOutputStream = new FileOutputStream($file);
        $fileOutputStream->write('foo');
        assertThat(file_get_contents($this->fileUrl), equals('foo'));
    }

    #[Test]
    #[RequiresPhpExtension('gd')]
    public function constructWithIllegalResource(): void
    {
        $illegalResource = imagecreate(2, 2);
        if (false === $illegalResource) {
            fail('Could not create illegal resource');
        }

        expect(fn() => new FileOutputStream($illegalResource))
            ->throws(InvalidArgumentException::class);
    }
}
