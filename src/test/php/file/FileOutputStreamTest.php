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
    fail,
    predicate\equals
};
/**
 * Test for stubbles\streams\file\FileOutputStream.
 *
 * @group  streams
 * @group  streams_file
 */
class FileOutputStreamTest extends TestCase
{
    /**
     * the file url used in the tests
     *
     * @type  string
     */
    private $fileUrl;

    protected function setUp(): void
    {
        vfsStream::setup('home');
        $this->fileUrl = vfsStream::url('home/test.txt');
    }

    /**
     * @test
     */
    public function constructWithStringCreatesFile()
    {
        new FileOutputStream($this->fileUrl);
        assertTrue(file_exists($this->fileUrl));
    }

    /**
     * @test
     */
    public function constructWithStringDelayedDoesNotCreateFile()
    {
        new FileOutputStream($this->fileUrl, 'wb', true);
        assertFalse(file_exists($this->fileUrl));
    }

    /**
     * @test
     */
    public function constructWithString()
    {
        $fileOutputStream = new FileOutputStream($this->fileUrl);
        $fileOutputStream->write('foo');
        assertThat(file_get_contents($this->fileUrl), equals('foo'));
    }

    /**
     * @test
     */
    public function constructWithStringDelayedCreatesFileOnWrite()
    {
        $fileOutputStream = new FileOutputStream($this->fileUrl, 'wb', true);
        $fileOutputStream->write('foo');
        assertTrue(file_exists($this->fileUrl));
    }

    /**
     * @test
     */
    public function constructWithStringFailsAndThrowsIOException()
    {
        vfsStream::newFile('test.txt', 0000)->at(vfsStream::setup());
        expect(function() { new FileOutputStream($this->fileUrl, 'r'); })
                ->throws(StreamException::class)
                ->withMessage(
                        'Can not open file vfs://home/test.txt with mode r:'
                        . ' failed to open stream: "org\bovigo\vfs\vfsStreamWrapper::stream_open"'
                        . ' call failed'
                );
    }

    /**
     * @test
     */
    public function constructWithResource()
    {
        $file = fopen($this->fileUrl, 'wb');
        if (false === $file) {
            fail('Could not open vfsStream file');
            return;
        }

        $fileOutputStream = new FileOutputStream($file);
        $fileOutputStream->write('foo');
        assertThat(file_get_contents($this->fileUrl), equals('foo'));
    }

    /**
     * @test
     * @requires  extension  gd
     */
    public function constructWithIllegalResource()
    {
        $illegalResource = imagecreate(2, 2);
        if (false === $illegalResource) {
            fail('Could not create illegal resource');
            return;
        }

        expect(function() use($illegalResource) { new FileOutputStream($illegalResource); })
                ->throws(\InvalidArgumentException::class);
    }
}
