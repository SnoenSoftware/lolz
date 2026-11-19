<?php

namespace App\Tests\Console;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Output\BufferedOutput;

class ProcessHelperTest extends TestCase
{
    public function testProcessHelperRunsWithoutDeprecations(): void
    {
        $deprecations = [];
        set_error_handler(function ($severity, $message) use (&$deprecations) {
            $deprecations[] = $message;
            return true;
        }, E_DEPRECATED | E_USER_DEPRECATED);
        try {
            $helper = new ProcessHelper();
            new HelperSet([$helper, new DebugFormatterHelper()]);
            foreach (['run', 'mustRun'] as $method) {
                $process = $helper->$method(new BufferedOutput(), [PHP_BINARY, '-r', 'echo "ok";']);
                self::assertTrue($process->isSuccessful());
                self::assertSame('ok', $process->getOutput());
            }
            self::assertSame([], $deprecations);
        } finally {
            restore_error_handler();
        }
    }
}
