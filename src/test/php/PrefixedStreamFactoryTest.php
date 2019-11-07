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

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\streams\PrefixedStreamFactory.
 *
 * @group  streams
 */
class PrefixedStreamFactoryTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\PrefixedStreamFactorys
     */
    private $prefixedStreamFactory;
    /**
     * @type  StreamFactory
     */
    private $streamFactory;

    protected function setUp(): void
    {
        $this->streamFactory = NewInstance::of(StreamFactory::class);
        $this->prefixedStreamFactory = new PrefixedStreamFactory(
                $this->streamFactory,
                'prefix/'
        );
    }

    /**
     * @test
     */
    public function inputStreamGetsPrefix()
    {
        $inputStream = NewInstance::of(InputStream::class);
        $this->streamFactory->returns(
                ['createInputStream' => $inputStream]
        );
        assertThat(
                $this->prefixedStreamFactory->createInputStream(
                        'foo',
                        ['bar' => 'baz']
                ),
                isSameAs($inputStream)
        );
        verify($this->streamFactory, 'createInputStream')
                ->received('prefix/foo', ['bar' => 'baz']);
    }

    /**
     * @test
     */
    public function outputStreamGetsPrefix()
    {
        $outputStream = NewInstance::of(OutputStream::class);
        $this->streamFactory->returns(
                ['createOutputStream' => $outputStream]
        );
        assertThat(
                $this->prefixedStreamFactory->createOutputStream(
                        'foo',
                        ['bar' => 'baz']
                ),
                isSameAs($outputStream)
        );
        verify($this->streamFactory, 'createOutputStream')
                ->received('prefix/foo', ['bar' => 'baz']);
    }
}
