<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\filter;
use PHPUnit\Framework\TestCase;
use stubbles\streams\memory\MemoryOutputStream;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\filter\FilteredOutputStream.
 *
 * @group streams
 * @group streams_filter
 */
class FilteredOutputStreamTest extends TestCase
{
    private FilteredOutputStream $filteredOutputStream;
    private MemoryOutputStream $memory;

    protected function setUp(): void
    {
        $this->memory = new MemoryOutputStream();
        $this->filteredOutputStream = new FilteredOutputStream(
            $this->memory,
            fn($value) => 'foo' === $value
        );
    }

    /**
     * @return array<scalar[]>
     */
    public static function writeData(): array
    {
        return [['foo', 3], ['bar', 0]];
    }

    /**
     * @test
     * @dataProvider writeData
     */
    public function returnsAmountOfDataBasedOnFilter(string $write, int $expected): void
    {
        assertThat($this->filteredOutputStream->write($write), equals($expected));
    }

    /**
     * @test
     */
    public function dataPassingTheFilterShouldBeWritten(): void
    {
        $this->filteredOutputStream->write('foo');
        assertThat($this->memory->buffer(), equals('foo'));
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWritten(): void
    {
        $this->filteredOutputStream->write('bar');
        assertEmptyString($this->memory->buffer());
    }

    /**
     * @test
     * @dataProvider writeData
     */
    public function returnsAmountOfDataBasedOnFilterPlusLineEnding(string $write, int $expected): void
    {
        if (0 < $expected) {
            $expected++;
        }

        assertThat(
            $this->filteredOutputStream->writeLine($write),
            equals($expected)
        );
    }

    /**
     * @test
     */
    public function dataPassingTheFilterShouldBeWrittenAsLine(): void
    {
        $this->filteredOutputStream->writeLine('foo');
        assertThat($this->memory->buffer(), equals("foo\n"));
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWrittenAsLine(): void
    {
        $this->filteredOutputStream->writeLine('bar');
        assertEmptyString($this->memory->buffer());
    }

    /**
     * @test
     * @since 3.2.0
     */
    public function writeLinesProcessesOnlyLinesSatisfyingFilter(): void
    {
        $this->filteredOutputStream->writeLines(['foo', 'bar']);
        assertThat($this->memory->buffer(), equals("foo\n"));
    }

    /**
     * @test
     * @since 8.0.0
     */
    public function writeLinesReturnsOnlyAmountOfUnfilteredBytedWritten(): void
    {
        assertThat(
            $this->filteredOutputStream->writeLines(['foo', 'bar']),
            equals(4)
        );
    }
}
