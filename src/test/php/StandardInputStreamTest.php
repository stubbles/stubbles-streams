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
use function bovigo\assert\assert;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\StandardInputStream.
 *
 * @group  streams
 * @since  5.4.0
 */
class StandardInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type  \stubbles\streams\StandardInputStream
     */
    private $standardInputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->standardInputStream = new StandardInputStream();
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function startsAtPositionZero()
    {
        assert($this->standardInputStream->tell(), equals(0));
    }

    /**
     * @test
     */
    public function seekAfterCloseThrowsLogicException()
    {
        $this->standardInputStream->close();
        expect(function() { $this->standardInputStream->seek(0); })
                ->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function canSeekToStartOfStream()
    {
        expect(function() { $this->standardInputStream->seek(0); })
                ->doesNotThrow();
    }

    /**
     * @test
     */
    public function canSeekToAnyPosition()
    {
        expect(function() { $this->standardInputStream->seek(100); })
                ->doesNotThrow();
    }

    /**
     * @test
     */
    public function tellAfterCloseThrowsLogicException()
    {
        $this->standardInputStream->close();
        expect(function() { $this->standardInputStream->tell(); })
                ->throws(\LogicException::class);
    }

    /**
     * @test
     * @since  8.0.0
     */
    public function tellAfterExternalCloseThrowsStreamException()
    {
        $stdInputStream = new class() extends StandardInputStream
        {
            public function __construct()
            {
                parent::__construct();
                fclose($this->handle);
            }
        };
        expect(function() use ($stdInputStream) { $stdInputStream->tell(); })
                ->throws(StreamException::class);
    }
}
