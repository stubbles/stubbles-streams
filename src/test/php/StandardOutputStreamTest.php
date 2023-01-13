<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\streams\StandardOutputStream.
 *
 * @group streams
 * @since 5.4.0
 */
class StandardOutputStreamTest extends TestCase
{
    /**
     * @test
     */
    public function writesToStandardOutputBuffer(): void
    {
        $out = new StandardOutputStream();
        ob_start();
        $out->write('foo');
        assertThat(ob_get_contents(), equals('foo'));
        ob_end_clean();
    }
}
