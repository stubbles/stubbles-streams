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
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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
 * @since 5.2.0
 */
#[Group('streams')]
class FunctionsTest extends TestCase
{
    private vfsStreamFile $file;

    protected function setUp(): void
    {
        $root       = vfsStream::setup();
        $this->file = vfsStream::newFile('test.txt')
            ->withContent("\nfoo\n\n")
            ->at($root);
    }

    #[Test]
    public function linesOfReturnsSequence(): void
    {
        assertThat(linesOf($this->file->url()), isInstanceOf(Sequence::class));
    }

    /**
     * @since 6.2.0
     */
    #[Test]
    public function nonEmptyLinesOfReturnsNonEmptyLinesOnly(): void
    {
        assertThat(
            nonEmptyLinesOf($this->file->url()),
            isNotEmpty()->and(each(equals('foo')))
        );
    }

    /**
     * @since 8.1.0
     */
    #[Test]
    #[Group('issue_1')]
    public function copyFromEmptyInputStreamResultsInNoBytesCopied(): void
    {
        $in  = new MemoryInputStream('');
        $out = new MemoryOutputStream();
        assertThat(copy($in)->to($out), equals(0));
    }

    /**
     * @since 8.1.0
     */
    #[Test]
    #[Group('issue_1')]
    public function copyFromEmptyInputStreamResultsInNothingReceivedOnOutputStream(): void
    {
        $in  = new MemoryInputStream('');
        $out = new MemoryOutputStream();
        copy($in)->to($out);
        assertEmptyString($out->buffer());
    }

    /**
     * @since 8.1.0
     */
    #[Test]
    #[Group('issue_1')]
    public function copyFromInputStreamResultsInAllBytesCopied(): void
    {
        $in  = new MemoryInputStream("foo\nbar\nbaz");
        $out = new MemoryOutputStream();
        assertThat(copy($in)->to($out), equals(11));
    }

    /**
     * @since 8.1.0
     */
    #[Test]
    #[Group('issue_1')]
    public function copyFromInputStreamWritesExactCopyToOutputStream(): void
    {
        $in  = new MemoryInputStream("foo\nbar\nbaz");
        $out = new MemoryOutputStream();
        copy($in)->to($out);
        assertThat($out->buffer(), equals("foo\nbar\nbaz"));
    }
}
