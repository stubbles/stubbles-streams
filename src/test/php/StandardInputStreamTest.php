<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\streams
 */
namespace stubbles\streams;
use function bovigo\assert\expect;
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
     */
    public function seekAfterCloseThrowsLogicException()
    {
        expect(function() {
                $this->standardInputStream->close();
                $this->standardInputStream->seek(0);
        })
        ->throws(\LogicException::class);
    }

    /**
     * @test
     */
    public function canSeekToStartOfStream()
    {
        expect(function() {
                $this->standardInputStream->seek(0);
        })
        ->doesNotThrow();
    }

    /**
     * @test
     */
    public function canSeekToAnyPosition()
    {
        expect(function() {
                $this->standardInputStream->seek(100);
        })
        ->doesNotThrow();
    }

    /**
     * @test
     */
    public function tellAfterCloseThrowsLogicException()
    {
        expect(function() {
                $this->standardInputStream->close();
                $this->standardInputStream->tell();
        })
        ->throws(\LogicException::class);
    }
}
