<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\onConsecutiveCalls;
/**
 * Test for stubbles\streams\InputStreamIterator.
 * @since 5.2.0
 */
#[Group('streams')]
class InputStreamIteratorTest extends TestCase
{
    #[Test]
    public function canIterateOverSeekableInputStream(): void
    {
        $content = [];
        foreach (linesOf(new MemoryInputStream("foo\nbar\nbaz\n")) as $lineNumber => $line) {
            $content[$lineNumber] = $line;
        }

        assertThat($content, equals([1 => 'foo', 2 => 'bar', 3 => 'baz']));
    }

    #[Test]
    public function canRewindSeekableInputStream(): void
    {
        $lines = linesOf(new MemoryInputStream("foo\nbar\nbaz\n"));
        foreach ($lines as $lineNumber => $line) {
            // do nothing
        }

        $content = [];
        foreach ($lines as $lineNumber => $line) {
            $content[$lineNumber] = $line;
        }

        assertThat($content, equals([1 => 'foo', 2 => 'bar', 3 => 'baz']));
    }

    #[Test]
    public function canIterateOverNonSeekableInputStream(): void
    {
        $inputStream = NewInstance::of(InputStream::class)->returns([
                'readLine' => onConsecutiveCalls('foo', 'bar', 'baz', ''),
                'eof'      => onConsecutiveCalls(false, false, false, true)
        ]);
        $content = [];
        foreach (linesOf($inputStream) as $lineNumber => $line) {
            $content[$lineNumber] = $line;
        }

        assertThat($content, equals([1 => 'foo', 2 => 'bar', 3 => 'baz']));
    }

    #[Test]
    public function canNotRewindNonSeekableInputStream(): void
    {
        $inputStream = NewInstance::of(InputStream::class)->returns([
                'readLine' => onConsecutiveCalls('foo', 'bar', 'baz', ''),
                'eof'      => onConsecutiveCalls(false, false, true, true)
        ]);
        $lines = linesOf($inputStream);
        foreach ($lines as $lineNumber => $line) {
            // do nothing
        }

        $content = [];
        foreach (linesOf($inputStream) as $lineNumber => $line) {
            $content[$lineNumber] = $line;
        }

        assertEmptyArray($content);
    }
}
