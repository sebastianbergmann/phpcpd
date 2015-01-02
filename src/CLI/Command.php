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

use SebastianBergmann\PHPCPD\Detector\Detector;
use SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy;
use SebastianBergmann\PHPCPD\Log\PMD;
use SebastianBergmann\PHPCPD\Log\Text;
use SebastianBergmann\FinderFacade\FinderFacade;
use Symfony\Component\Console\Command\Command as AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 2.0.0
 */
class Command extends AbstractCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('phpcpd')
             ->setDefinition(
                 array(
                     new InputArgument(
                         'values',
                         InputArgument::IS_ARRAY,
                         'Files and directories to analyze'
                     )
                 )
             )
             ->addOption(
                 'names',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'A comma-separated list of file names to check',
                 array('*.php')
             )
             ->addOption(
                 'names-exclude',
                 null,
                 InputOption::VALUE_REQUIRED,
                 'A comma-separated list of file names to exclude',
                 array()
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
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|integer null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new FinderFacade(
            $input->getArgument('values'),
            $input->getOption('exclude'),
            $this->handleCSVOption($input, 'names'),
            $this->handleCSVOption($input, 'names-exclude')
        );

        $files = $finder->findFiles();

        if (empty($files)) {
            $output->writeln('No files found to scan');
            exit(1);
        }

        $progressHelper = null;

        if ($input->getOption('progress')) {
            $progressHelper = $this->getHelperSet()->get('progress');
            $progressHelper->start($output, count($files));
        }

        $strategy = new DefaultStrategy;
        $detector = new Detector($strategy, $progressHelper);
        $quiet    = $output->getVerbosity() == OutputInterface::VERBOSITY_QUIET;

        $clones = $detector->copyPasteDetection(
            $files,
            $input->getOption('min-lines'),
            $input->getOption('min-tokens'),
            $input->getOption('fuzzy')
        );

        if ($input->getOption('progress')) {
            $progressHelper->finish();
            $output->writeln('');
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
            print \PHP_Timer::resourceUsage() . "\n";
        }

        if (count($clones) > 0) {
            exit(1);
        }
    }

    /**
     * @param  Symfony\Component\Console\Input\InputOption $input
     * @param  string                                      $option
     * @return array
     */
    private function handleCSVOption(InputInterface $input, $option)
    {
        $result = $input->getOption($option);

        if (!is_array($result)) {
            $result = explode(',', $result);
            array_map('trim', $result);
        }

        return $result;
    }
}
