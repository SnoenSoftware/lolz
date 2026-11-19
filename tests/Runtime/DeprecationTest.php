<?php

namespace App\Tests\Runtime;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class DeprecationTest extends TestCase
{
    public function testProductionRuntimeAndSchedulerHaveNoDeprecationOutput(): void
    {
        $filesystem = new Filesystem();
        $directory = $filesystem->tempnam(sys_get_temp_dir(), 'lolz-runtime-');
        $filesystem->remove($directory);
        $filesystem->mkdir($directory);
        $root = dirname(__DIR__, 2);
        $environment = [
            'APP_ENV' => 'prod',
            'APP_DEBUG' => '0',
            'DATABASE_URL' => 'sqlite:///'.$directory.'/db.sqlite',
            'LOLZ_CRUNZ_MARKER' => $directory.'/scheduled',
        ];
        try {
            $commands = [
                [PHP_BINARY, '-d', 'error_reporting=-1', '-d', 'zend.assertions=1', $root.'/tests/Fixtures/runtime.php'],
                [PHP_BINARY, '-d', 'error_reporting=-1', $root.'/vendor/bin/crunz', 'schedule:list'],
                [PHP_BINARY, '-d', 'error_reporting=-1', $root.'/vendor/bin/crunz', 'schedule:run', $root.'/tests/Fixtures', '--force'],
            ];
            foreach ($commands as $command) {
                $process = new Process($command, $root, $environment);
                $process->run();
                $output = $process->getOutput().$process->getErrorOutput();
                self::assertSame(0, $process->getExitCode(), $output);
                self::assertDoesNotMatchRegularExpression('/deprecat/i', $output);
            }
            self::assertSame('ran', file_get_contents($directory.'/scheduled'));
        } finally {
            $filesystem->remove($directory);
        }
    }
}
