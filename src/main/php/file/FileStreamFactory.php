<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\streams\file;
use stubbles\streams\StreamFactory;
/**
 * Factory for file streams.
 */
class FileStreamFactory implements StreamFactory
{
    /**
     * @param  int  $fileMode  default file mode if directory for output stream should be created
     * @Property('stubbles.filemode')
     */
    public function __construct(private int $fileMode = 0700) { }

    /**
     * creates an input stream for given source
     *
     * @param mixed               $source  source to create input stream from
     * @param array<string,mixed> $options list of options for the input stream
     */
    public function createInputStream(mixed $source, array $options = []): FileInputStream
    {
        if (isset($options['filemode'])) {
            return new FileInputStream($source, $options['filemode']);
        }

        return new FileInputStream($source);
    }

    /**
     * creates an output stream for given target
     *
     * @param mixed               $target  target to create output stream for
     * @param array<string,mixed> $options list of options for the output stream
     */
    public function createOutputStream(mixed $target, array $options = []): FileOutputStream
    {
        if (isset($options['createDirIfNotExists']) && true === $options['createDirIfNotExists']) {
            $dir = dirname($target);
            if (!file_exists($dir)) {
                $filemode = $options['dirPermissions'] ?? $this->fileMode;
                mkdir($dir, $filemode, true);
            }
        }

        $filemode = $options['filemode'] ?? 'wb';
        $delayed  = $options['delayed']  ?? false;
        return new FileOutputStream($target, $filemode, $delayed);
    }
}
