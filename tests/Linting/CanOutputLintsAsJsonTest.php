<?php

namespace tests\Linting;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tighten\Commands\LintCommand;
use Tighten\Linters\ConcatenationSpacing;

class CanOutputLintsAsJsonTest extends TestCase
{
    /** @test */
    function can_use_json_flag_with_lints()
    {
        $application = new Application;
        $command = new LintCommand;
        $application->add($command);
        $commandTester = new CommandTester($command);

        $file = <<<file
<?php

echo 'a'.'b';

file;

        $filePath = tempnam(sys_get_temp_dir(), 'test');

        file_put_contents($filePath, $file);

        $commandTester->execute([
            'command'  => $command->getName(),
            'file or directory' => $filePath,
            '--json' => true,
        ]);

        $this->assertEquals([
            'errors' => [
                [
                    'line' => 3,
                    'message' => '! ' . ConcatenationSpacing::description,
                    'source' => 'ConcatenationSpacing',
                ],
            ],
        ], json_decode($commandTester->getDisplay(), true));

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
    /** @test */
    function can_use_json_flag_without_lints()
    {
        $application = new Application;
        $command = new LintCommand;
        $application->add($command);
        $commandTester = new CommandTester($command);

        $file = <<<file
<?php

echo 'a';

file;

        $filePath = tempnam(sys_get_temp_dir(), 'test');

        file_put_contents($filePath, $file);

        $commandTester->execute([
            'command'  => $command->getName(),
            'file or directory' => $filePath,
            '--json' => true,
        ]);

        $this->assertEquals([
            'errors' => [],
        ], json_decode($commandTester->getDisplay(), true));

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
