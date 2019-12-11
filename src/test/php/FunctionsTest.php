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
use stubbles\sequence\Sequence;
use PHPUnit\Framework\TestCase;
use stubbles\streams\memory\{MemoryInputStream, MemoryOutputStream};

use function bovigo\assert\{
    assertThat,
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
class FunctionsTest extends TestCase
{
    /**
     * @var  \org\bovigo\vfs\vfsStreamFile
     */
    private $file;

    protected function setUp(): void
    {
        $root       = vfsStream::setup();
        $this->file = vfsStream::newFile('test.txt')
                ->withContent("\nfoo\n\n")
                ->at($root);
    }

    /**
     * @test
     */
    public function linesOfReturnsSequence(): void
    {
        assertThat(linesOf($this->file->url()), isInstanceOf(Sequence::class));
    }

    /**
     * @test
     * @since  6.2.0
     */
    public function nonEmptyLinesOfReturnsNonEmptyLinesOnly(): void
    {
        assertThat(
                nonEmptyLinesOf($this->file->url()),
                isNotEmpty()->and(each(equals('foo')))
        );
    }

    /**
     * @test
     * @group  issue_1
     * @since  8.1.0
     */
    public function copyFromEmptyInputStreamResultsInNoBytesCopied(): void
    {
        $in  = new MemoryInputStream('');
        $out = new MemoryOutputStream();
        assertThat(copy($in)->to($out), equals(0));
    }

    /**
     * @test
     * @group  issue_1
     * @since  8.1.0
     */
    public function copyFromEmptyInputStreamResultsInNothingReceivedOnOutputStream(): void
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
    public function copyFromInputStreamResultsInAllBytesCopied(): void
    {
        $in  = new MemoryInputStream("foo\nbar\nbaz");
        $out = new MemoryOutputStream();
        assertThat(copy($in)->to($out), equals(11));
    }

    /**
     * @test
     * @group  issue_1
     * @since  8.1.0
     */
    public function copyFromInputStreamWritesExactCopyToOutputStream(): void
    {
        $in  = new MemoryInputStream("foo\nbar\nbaz");
        $out = new MemoryOutputStream();
        copy($in)->to($out);
        assertThat($out->buffer(), equals("foo\nbar\nbaz"));
    }
}
