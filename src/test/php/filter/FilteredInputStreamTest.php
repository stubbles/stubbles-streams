<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\filter;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\filter\FilteredInputStream.
 */
#[Group('streams')]
#[Group('streams_filter')]
class FilteredInputStreamTest extends TestCase
{
    private FilteredInputStream $filteredInputStream;
    private MemoryInputStream $inputStream;

    protected function setUp(): void
    {
        $this->inputStream = new MemoryInputStream("foo\nbar");
        $this->filteredInputStream = new FilteredInputStream(
            $this->inputStream,
            fn(string $value): bool => 'bar' === $value
        );
    }

    #[Test]
    public function readReturnsEmptyStringIfChunkIsFiltered(): void
    {
        assertEmptyString($this->filteredInputStream->read());
    }

    /**
     * @since 8.0.0
     */
    #[Test]
    public function readReturnsChunkIfChunkWithSpecifiedSizeSatisfiesFilter(): void
    {
        assertThat($this->filteredInputStream->read(4), equals('bar'));
    }

    #[Test]
    public function readLineReturnsUnfilteredLinesOnly(): void
    {
        assertThat($this->filteredInputStream->readLine(), equals('bar'));
    }
}
