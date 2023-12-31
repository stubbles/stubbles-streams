<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\file;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\streams\StreamException;

use function bovigo\assert\{
    assertThat,
    assertFalse,
    assertTrue,
    expect,
    predicate\equals
};
use function stubbles\reflect\annotationsOfConstructor;
/**
 * Test for stubbles\streams\file\FileStreamFactory.
 */
#[Group('streams')]
#[Group('streams_file')]
class FileStreamFactoryTest extends TestCase
{
    private FileStreamFactory $fileStreamFactory;
    private string $fileUrl;
    private string $fileUrl2;
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('home');
        vfsStream::newFile('in.txt')->at($this->root)->withContent('foo');
        $this->fileUrl           = vfsStream::url('home/out.txt');
        $this->fileUrl2          = vfsStream::url('home/test/out.txt');
        $this->fileStreamFactory = new FileStreamFactory();
    }

    #[Test]
    public function annotationsPresent(): void
    {
        $annotations = annotationsOfConstructor($this->fileStreamFactory);
        assertTrue($annotations->contain('Property'));
        assertThat(
            $annotations->named('Property')[0]->getName(),
            equals('stubbles.filemode')
        );
    }

    #[Test]
    public function createInputStreamWithOptions(): void
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(
            vfsStream::url('home/in.txt'),
            ['filemode' => 'rb']
        );
        assertThat($fileInputStream->readLine(), equals('foo'));
    }

    #[Test]
    public function createInputStreamWithoutOptions(): void
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(
            vfsStream::url('home/in.txt')
        );
        assertThat($fileInputStream->readLine(), equals('foo'));
    }

    #[Test]
    public function createOutputStreamWithFilemodeOption(): void
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
            $this->fileUrl,
            ['filemode' => 'wb']
        );
        $fileOutputStream->write('foo');
        assertThat(file_get_contents($this->fileUrl), equals('foo'));
    }

    #[Test]
    public function createOutputStreamWithFilemodeOptionAndDirectoryOptionSetToTrue(): void
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
            $this->fileUrl2,
            [
                'filemode'             => 'wb',
                'createDirIfNotExists' => true
            ]
        );
        $fileOutputStream->write('foo');
        assertThat(file_get_contents($this->fileUrl2), equals('foo'));
    }

    #[Test]
    public function createOutputStreamWithDirectoryOptionNotSetThrowsExceptionIfDirectoryDoesNotExist(): void
    {
        assertFalse(file_exists($this->fileUrl2));
        expect(fn() => $this->fileStreamFactory->createOutputStream($this->fileUrl2))
            ->throws(StreamException::class);
    }

    #[Test]
    public function createOutputStreamWithDirectoryOptionSetToFalseThrowsExceptionIfDirectoryDoesNotExist(): void
    {
        expect(fn() =>
            $this->fileStreamFactory->createOutputStream(
                $this->fileUrl2,
                ['createDirIfNotExists' => false]
            )
        )
            ->throws(StreamException::class);
    }

    #[Test]
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithDefaultPermissions(): void
    {
        $this->fileStreamFactory->createOutputStream(
            $this->fileUrl2,
            ['createDirIfNotExists' => true]
        );
        assertThat($this->root->getChild('test')->getPermissions(), equals(0700));
    }

    #[Test]
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithOptionsPermissions(): void
    {
        $this->fileStreamFactory->createOutputStream(
            $this->fileUrl2,
            [
                'createDirIfNotExists' => true,
                'dirPermissions'       => 0666
            ]
        );
        assertThat($this->root->getChild('test')->getPermissions(), equals(0666));
    }

    #[Test]
    public function createOutputStreamWithDelayedOption(): void
    {
        $this->fileStreamFactory->createOutputStream(
            $this->fileUrl,
            ['delayed' => true]
        );
        assertFalse(file_exists($this->fileUrl));
    }

    #[Test]
    public function createOutputStreamWithoutOptions(): void
    {
        $this->fileStreamFactory->createOutputStream($this->fileUrl);
        assertTrue(file_exists($this->fileUrl));
    }
}
