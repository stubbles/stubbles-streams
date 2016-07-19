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
use bovigo\callmap\NewInstance;
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\onConsecutiveCalls;
/**
 * Test for stubbles\streams\InputStreamIterator.
 *
 * @group  streams
 * @since  5.2.0
 */
class InputStreamIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function canIterateOverSeekableInputStream()
    {
        $expectedLineNumber = 1;
        $expectedLine = [1 => 'foo', 2 => 'bar', 3 => 'baz', 4 => ''];
        foreach (linesOf(new MemoryInputStream("foo\nbar\nbaz\n")) as $lineNumber => $line) {
            assert($lineNumber, equals($expectedLineNumber));
            assert($line, equals($expectedLine[$expectedLineNumber]));
            $expectedLineNumber++;
        }
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

        $expectedLineNumber = 1;
        $expectedLine = [1 => 'foo', 2 => 'bar', 3 => 'baz', 4 => ''];
        foreach ($lines as $lineNumber => $line) {
            assert($lineNumber, equals($expectedLineNumber));
            assert($line, equals($expectedLine[$expectedLineNumber]));
            $expectedLineNumber++;
        }
    }

    /**
     * @test
     */
    public function canIterateOverNonSeekableInputStream()
    {
        $inputStream = NewInstance::of(InputStream::class)->mapCalls([
                'readLine' => onConsecutiveCalls('foo', 'bar', 'baz', ''),
                'eof'      => onConsecutiveCalls(false, false, false, true)
        ]);
        $expectedLineNumber = 1;
        $expectedLine = [1 => 'foo', 2 => 'bar', 3 => 'baz', 4 => ''];
        foreach (linesOf($inputStream) as $lineNumber => $line) {
            assert($lineNumber, equals($expectedLineNumber));
            assert($line, equals($expectedLine[$expectedLineNumber]));
            $expectedLineNumber++;
        }
    }

    /**
     * @test
     */
    public function canNotRewindNonSeekableInputStream()
    {
        $inputStream = NewInstance::of(InputStream::class)->mapCalls([
                'readLine' => onConsecutiveCalls('foo', 'bar', 'baz', '', ''),
                'eof'      => onConsecutiveCalls(false, false, false, true, true)
        ]);
        $lines = linesOf($inputStream);
        foreach ($lines as $lineNumber => $line) {
            // do nothing
        }

        $count = 0;
        foreach (linesOf($inputStream) as $lineNumber => $line) {
            $count++;
        }

        assert($count, equals(0));
    }
}
