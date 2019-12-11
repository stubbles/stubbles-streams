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
 *
 * @group  streams
 * @group  streams_file
 */
class FileStreamFactoryTest extends TestCase
{
    /**
     * instance to test
     *
     * @var  FileStreamFactory
     */
    private $fileStreamFactory;
    /**
     * a file url used in the tests
     *
     * @var  string
     */
    private $fileUrl;
    /**
     * a file url used in the tests
     *
     * @var  string
     */
    private $fileUrl2;
    /**
     * root directory
     *
     * @var  \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('home');
        vfsStream::newFile('in.txt')->at($this->root)->withContent('foo');
        $this->fileUrl           = vfsStream::url('home/out.txt');
        $this->fileUrl2          = vfsStream::url('home/test/out.txt');
        $this->fileStreamFactory = new FileStreamFactory();
    }

    /**
     * @test
     */
    public function annotationsPresent(): void
    {
        $annotations = annotationsOfConstructor($this->fileStreamFactory);
        assertTrue($annotations->contain('Property'));
        assertThat(
                $annotations->named('Property')[0]->getName(),
                equals('stubbles.filemode')
        );
    }

    /**
     * @test
     */
    public function createInputStreamWithOptions(): void
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(
                vfsStream::url('home/in.txt'),
                ['filemode' => 'rb']
        );
        assertThat($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function createInputStreamWithoutOptions(): void
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(
                vfsStream::url('home/in.txt')
        );
        assertThat($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function createOutputStreamWithFilemodeOption(): void
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl,
                ['filemode' => 'wb']
        );
        $fileOutputStream->write('foo');
        assertThat(file_get_contents($this->fileUrl), equals('foo'));
    }

    /**
     * @test
     */
    public function createOutputStreamWithFilemodeOptionAndDirectoryOptionSetToTrue(): void
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl2,
                ['filemode'             => 'wb',
                 'createDirIfNotExists' => true
                ]
        );
        $fileOutputStream->write('foo');
        assertThat(file_get_contents($this->fileUrl2), equals('foo'));
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionNotSetThrowsExceptionIfDirectoryDoesNotExist(): void
    {
        assertFalse(file_exists($this->fileUrl2));
        expect(function() {
                $this->fileStreamFactory->createOutputStream($this->fileUrl2);
        })
        ->throws(StreamException::class);
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionSetToFalseThrowsExceptionIfDirectoryDoesNotExist(): void
    {
        expect(function() {
                $this->fileStreamFactory->createOutputStream(
                        $this->fileUrl2,
                        ['createDirIfNotExists' => false]
                );
        })
        ->throws(StreamException::class);
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithDefaultPermissions(): void
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl2,
                ['createDirIfNotExists' => true]
        );
        assertThat($this->root->getChild('test')->getPermissions(), equals(0700));
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithOptionsPermissions(): void
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl2,
                ['createDirIfNotExists' => true,
                 'dirPermissions'       => 0666
                ]
        );
        assertThat($this->root->getChild('test')->getPermissions(), equals(0666));
    }

    /**
     * @test
     */
    public function createOutputStreamWithDelayedOption(): void
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl,
                ['delayed' => true]
        );
        assertFalse(file_exists($this->fileUrl));
    }

    /**
     * @test
     */
    public function createOutputStreamWithoutOptions(): void
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl
        );
        assertTrue(file_exists($this->fileUrl));
    }
}
