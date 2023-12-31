<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;

use LogicException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\StandardInputStream.
 *
 * @since 5.4.0
 */
#[Group('streams')]
class StandardInputStreamTest extends TestCase
{
    private StandardInputStream $standardInputStream;

    protected function setUp(): void
    {
        $this->standardInputStream = new StandardInputStream();
    }

    /**
     * @since 8.0.0
     */
    #[Test]
    public function startsAtPositionZero(): void
    {
        assertThat($this->standardInputStream->tell(), equals(0));
    }

    #[Test]
    public function seekAfterCloseThrowsLogicException(): void
    {
        $this->standardInputStream->close();
        expect(fn() => $this->standardInputStream->seek(0))
            ->throws(LogicException::class);
    }

    #[Test]
    public function canSeekToStartOfStream(): void
    {
        expect(fn() => $this->standardInputStream->seek(0))
            ->doesNotThrow();
    }

    #[Test]
    public function tellAfterCloseThrowsLogicException(): void
    {
        $this->standardInputStream->close();
        expect(fn() => $this->standardInputStream->tell())
            ->throws(LogicException::class);
    }

    /**
     * @since 8.0.0
     */
    #[Test]
    public function tellAfterExternalCloseThrowsStreamException(): void
    {
        $stdInputStream = new class() extends StandardInputStream
        {
            public function __construct()
            {
                parent::__construct();
                if (null !== $this->handle) {
                    fclose($this->handle);
                }
            }
        };
        expect(fn() => $stdInputStream->tell())
            ->throws(LogicException::class);
    }
}
