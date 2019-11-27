<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\CLI;

use SebastianBergmann\FinderFacade\FinderFacade;
use SebastianBergmann\PHPCPD\Detector\Detector;
use SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy;
use SebastianBergmann\PHPCPD\Log\PMD;
use SebastianBergmann\PHPCPD\Log\Text;
use SebastianBergmann\Timer\Timer;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class Command extends AbstractCommand
{
    /**
     * Configures the current command.
     */
    protected function configure(): void
    {
        $this->setName('phpcpd')
             ->setDefinition(
                 [
                     new InputArgument(
                         'values',
                         InputArgument::IS_ARRAY,
                         'Files and directories to analyze'
                     ),
                 ]
             )
             ->addOption(
                 'names',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'A comma-separated list of file names to check',
                 ['*.php']
             )
             ->addOption(
                 'names-exclude',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'A comma-separated list of file names to exclude',
                 []
             )
             ->addOption(
                 'regexps-exclude',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'A comma-separated list of paths regexps to exclude (example: "#var/.*_tmp#")',
                 []
             )
             ->addOption(
                 'exclude',
                 null,
                 InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                 'Exclude a directory from code analysis (must be relative to source)'
             )
             ->addOption(
                 'log-pmd',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Write result in PMD-CPD XML format to file'
             )
             ->addOption(
                 'min-lines',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Minimum number of identical lines',
                 5
             )
             ->addOption(
                 'min-tokens',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'Minimum number of identical tokens',
                 70
             )
             ->addOption(
                 'fuzzy',
                 null,
                 InputOption::VALUE_NONE,
                 'Fuzz variable names'
             )
             ->addOption(
                 'progress',
                 null,
                 InputOption::VALUE_NONE,
                 'Show progress bar'
             );
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new FinderFacade(
            $input->getArgument('values'),
            $input->getOption('exclude'),
            $this->handleCSVOption($input, 'names'),
            $this->handleCSVOption($input, 'names-exclude'),
            $this->handleCSVOption($input, 'regexps-exclude')
        );

        $files = $finder->findFiles();

        if (empty($files)) {
            $output->writeln('No files found to scan');
            return 0;
        }

        $progressBar = null;

        if ($input->getOption('progress')) {
            $progressBar = new ProgressBar($output, \count($files));
            $progressBar->start();
        }

        $strategy = new DefaultStrategy;
        $detector = new Detector($strategy, $progressBar);
        $quiet    = $output->getVerbosity() == OutputInterface::VERBOSITY_QUIET;

        $clones = $detector->copyPasteDetection(
            $files,
            $input->getOption('min-lines'),
            $input->getOption('min-tokens'),
            $input->getOption('fuzzy')
        );

        if ($input->getOption('progress')) {
            $progressBar->finish();
            $output->writeln("\n");
        }

        if (!$quiet) {
            $printer = new Text;
            $printer->printResult($output, $clones);
            unset($printer);
        }

        $logPmd = $input->getOption('log-pmd');

        if ($logPmd) {
            $pmd = new PMD($logPmd);
            $pmd->processClones($clones);
            unset($pmd);
        }

        if (!$quiet) {
            print Timer::resourceUsage() . "\n";
        }

        if (\count($clones) > 0) {
            return 1;
        }

        return 0;
    }

    private function handleCSVOption(InputInterface $input, string $option): array
    {
        $result = $input->getOption($option);

        if (!\is_array($result)) {
            $result = \explode(',', $result);

            \array_map('trim', $result);
        }

        return $result;
    }
}
