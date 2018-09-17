<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\CLI;

use SebastianBergmann\Version;
use Symfony\Component\Console\Application as AbstractApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Application extends AbstractApplication
{
    public function __construct()
    {
        $version = new Version('4.1.0', \dirname(__DIR__, 2));

        parent::__construct('phpcpd', $version->getVersion());
    }

    /**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }

    /**
     * Runs the current application.
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        $this->disableXdebug();

        if (!$input->hasParameterOption('--quiet')) {
            $output->write(
                \sprintf(
                    "phpcpd %s by Sebastian Bergmann.\n\n",
                    $this->getVersion()
                )
            );
        }

        if ($input->hasParameterOption('--version') ||
            $input->hasParameterOption('-V')) {
            exit;
        }

        if (!$input->getFirstArgument()) {
            $input = new ArrayInput(['--help']);
        }

        return (int) parent::doRun($input, $output);
    }

    /**
     * Gets the name of the command based on input.
     */
    protected function getCommandName(InputInterface $input): string
    {
        return 'phpcpd';
    }

    /**
     * Gets the default commands that should always be available.
     */
    protected function getDefaultCommands(): array
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new Command;

        return $defaultCommands;
    }

    private function disableXdebug(): void
    {
        if (!\extension_loaded('xdebug')) {
            return;
        }

        \ini_set('xdebug.scream', 0);
        \ini_set('xdebug.max_nesting_level', 8192);
        \ini_set('xdebug.show_exception_trace', 0);
        \ini_set('xdebug.show_error_trace', 0);

        \xdebug_disable();
    }
}
