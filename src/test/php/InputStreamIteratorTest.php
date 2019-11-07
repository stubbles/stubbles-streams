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
use PHPUnit\Framework\TestCase;
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\onConsecutiveCalls;
/**
 * Test for stubbles\streams\InputStreamIterator.
 *
 * @group  streams
 * @since  5.2.0
 */
class InputStreamIteratorTest extends TestCase
{
    /**
     * @test
     */
    public function canIterateOverSeekableInputStream()
    {
        $content = [];
        foreach (linesOf(new MemoryInputStream("foo\nbar\nbaz\n")) as $lineNumber => $line) {
            $content[$lineNumber] = $line;
        }

        assertThat($content, equals([1 => 'foo', 2 => 'bar', 3 => 'baz']));
    }

    /**
     * @test
     */
    public function canRewindSeekableInputStream()
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

    /**
     * @test
     */
    public function canIterateOverNonSeekableInputStream()
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

    /**
     * @test
     */
    public function canNotRewindNonSeekableInputStream()
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
