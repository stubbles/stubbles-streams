<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\memory;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Test for stubbles\streams\memory\MemoryStreamFactory.
 */
#[Group('streams')]
#[Group('streams_memory')]
class MemoryStreamFactoryTest extends TestCase
{
    private MemoryStreamFactory $memoryStreamFactory;

    protected function setUp(): void
    {
        $this->memoryStreamFactory = new MemoryStreamFactory();
    }

    #[Test]
    public function createInputStream(): void
    {
        $memoryInputStream = $this->memoryStreamFactory->createInputStream('buffer');
        assertThat($memoryInputStream, isInstanceOf(MemoryInputStream::class));
    }

    #[Test]
    public function createInputStreamUsesGivenStringAsStreamContent(): void
    {
        $memoryInputStream = $this->memoryStreamFactory->createInputStream('buffer');
        assertThat($memoryInputStream->readLine(), equals('buffer'));
    }

    #[Test]
    public function createOutputStream(): void
    {
        assertThat(
            $this->memoryStreamFactory->createOutputStream('buffer'),
            isInstanceOf(MemoryOutputStream::class)
        );
    }
}
