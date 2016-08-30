<?php
declare(strict_types=1);
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
use stubbles\sequence\Sequence;
use stubbles\streams\memory\{MemoryInputStream, MemoryOutputStream};

use function bovigo\assert\{
    assert,
    assertEmptyString,
    predicate\each,
    predicate\equals,
    predicate\isInstanceOf,
    predicate\isNotEmpty
};
/**
 * Tests for stubbles\streams\*().
 *
 * @since  5.2.0
 * @group  streams
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    private $file;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $root       = vfsStream::setup();
        $this->file = vfsStream::newFile('test.txt')
                ->withContent("\nfoo\n\n")
                ->at($root);
    }

    /**
     * @test
     */
    public function linesOfReturnsSequence()
    {
        assert(linesOf($this->file->url()), isInstanceOf(Sequence::class));
    }

    /**
     * @test
     * @since  6.2.0
     */
    public function nonEmptyLinesOfReturnsNonEmptyLinesOnly()
    {
        assert(
                nonEmptyLinesOf($this->file->url()),
                isNotEmpty()->and(each(equals('foo')))
        );
    }

    /**
     * @test
     * @group  issue_1
     * @since  8.1.0
     */
    public function copyFromEmptyInputStreamResultsInNoBytesCopied()
    {
        $in  = new MemoryInputStream('');
        $out = new MemoryOutputStream();
        assert(copy($in)->to($out), equals(0));
    }

    /**
     * @test
     * @group  issue_1
     * @since  8.1.0
     */
    public function copyFromEmptyInputStreamResultsInNothingReceivedOnOutputStream()
    {
        $in  = new MemoryInputStream('');
        $out = new MemoryOutputStream();
        copy($in)->to($out);
        assertEmptyString($out->buffer());
    }

    /**
     * @test
     * @group  issue_1
     * @since  8.1.0
     */
    public function copyFromInputStreamResultsInAllBytesCopied()
    {
        $in  = new MemoryInputStream("foo\nbar\nbaz");
        $out = new MemoryOutputStream();
        assert(copy($in)->to($out), equals(11));
    }

    /**
     * @test
     * @group  issue_1
     * @since  8.1.0
     */
    public function copyFromInputStreamWritesExactCopyToOutputStream()
    {
        $in  = new MemoryInputStream("foo\nbar\nbaz");
        $out = new MemoryOutputStream();
        copy($in)->to($out);
        assert($out->buffer(), equals("foo\nbar\nbaz"));
    }
}
