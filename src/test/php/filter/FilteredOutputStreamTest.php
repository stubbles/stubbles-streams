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
namespace stubbles\streams\filter;
use stubbles\streams\memory\MemoryOutputStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\filter\FilteredOutputStream.
 *
 * @group  streams
 * @group  streams_filter
 */
class FilteredOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\filter\FilteredOutputStream
     */
    private $filteredOutputStream;
    /**
     * decorated input stream
     *
     * @type  \stubbles\streams\memory\MemoryOutputStream
     */
    private $memory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memory = new MemoryOutputStream();
        $this->filteredOutputStream = new FilteredOutputStream(
                $this->memory,
                function($value)
                {
                    return 'foo' === $value;
                }
        );
    }

    public function writeData(): array
    {
        return [['foo', 3], ['bar', 0]];
    }

    /**
     * @test
     * @dataProvider writeData
     */
    public function returnsAmountOfDataBasedOnFilter($write, $expected)
    {
        assert($this->filteredOutputStream->write($write), equals($expected));
    }

    /**
     * @test
     */
    public function dataPassingTheFilterShouldBeWritten()
    {
        $this->filteredOutputStream->write('foo');
        assert($this->memory->buffer(), equals('foo'));
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWritten()
    {
        $this->filteredOutputStream->write('bar');
        assertEmptyString($this->memory->buffer());
    }

    /**
     * @test
     * @dataProvider writeData
     */
    public function returnsAmountOfDataBasedOnFilterPlusLineEnding($write, $expected)
    {
        if (0 < $expected) {
            $expected++;
        }
        assert(
                $this->filteredOutputStream->writeLine($write),
                equals($expected)
        );
    }

    /**
     * @test
     */
    public function dataPassingTheFilterShouldBeWrittenAsLine()
    {
        $this->filteredOutputStream->writeLine('foo');
        assert($this->memory->buffer(), equals("foo\n"));
    }

    /**
     * @test
     */
    public function dataNotPassingTheFilterShouldNotBeWrittenAsLine()
    {
        $this->filteredOutputStream->writeLine('bar');
        assertEmptyString($this->memory->buffer());
    }

    /**
     * @test
     * @since  3.2.0
     */
    public function writeLinesProcessesOnlyLinesSatisfyingFilter()
    {
        $this->filteredOutputStream->writeLines(['foo', 'bar']);
        assert($this->memory->buffer(), equals("foo\n"));
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function writeLinesReturnsOnlyAmountOfUnfilteredBytedWritten()
    {
        assert(
                $this->filteredOutputStream->writeLines(['foo', 'bar']),
                equals(4)
        );
    }
}
