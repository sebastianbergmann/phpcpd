<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009-2013, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   phpcpd
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since     File available since Release 1.0.0
 */

namespace SebastianBergmann\PHPCPD\TextUI
{
    use SebastianBergmann\FinderFacade\FinderFacade;
    use SebastianBergmann\Version;
    use SebastianBergmann\PHPCPD\Detector\Detector;
    use SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy;
    use SebastianBergmann\PHPCPD\Log\PMD;

    /**
     * TextUI frontend for PHPCPD.
     *
     * @author    Sebastian Bergmann <sebastian@phpunit.de>
     * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
     * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
     * @link      http://github.com/sebastianbergmann/phpcpd/tree
     * @since     Class available since Release 1.0.0
     */
    class Command
    {
        private $version;

        public function __construct()
        {
            $version = new Version('1.4.3', __DIR__);
            $this->version = $version->getVersion();
        }

        /**
         * Main method.
         */
        public function main()
        {
            $input = new \ezcConsoleInput;

            $input->registerOption(
              new \ezcConsoleOption(
                '',
                'exclude',
                \ezcConsoleInput::TYPE_STRING,
                array(),
                TRUE
               )
            );

            $input->registerOption(
              new \ezcConsoleOption(
                'h',
                'help',
                \ezcConsoleInput::TYPE_NONE,
                NULL,
                FALSE,
                '',
                '',
                array(),
                array(),
                FALSE,
                FALSE,
                TRUE
               )
            );

            $input->registerOption(
              new \ezcConsoleOption(
                '',
                'log-pmd',
                \ezcConsoleInput::TYPE_STRING
               )
            );

            $input->registerOption(
              new \ezcConsoleOption(
                '',
                'min-lines',
                \ezcConsoleInput::TYPE_INT,
                5
               )
            );

            $input->registerOption(
              new \ezcConsoleOption(
                '',
                'min-tokens',
                \ezcConsoleInput::TYPE_INT,
                70
               )
            );

            $input->registerOption(
              new \ezcConsoleOption(
                '',
                'names',
                \ezcConsoleInput::TYPE_STRING,
                '*.php',
                FALSE
               )
            );

            $input->registerOption(
              new \ezcConsoleOption(
                '',
                'quiet',
                \ezcConsoleInput::TYPE_NONE,
                NULL,
                FALSE
               )
            );

            $input->registerOption(
              new \ezcConsoleOption(
                'v',
                'version',
                \ezcConsoleInput::TYPE_NONE,
                NULL,
                FALSE,
                '',
                '',
                array(),
                array(),
                FALSE,
                FALSE,
                TRUE
               )
            );

            $input->registerOption(
              new \ezcConsoleOption(
                '',
                'progress',
                \ezcConsoleInput::TYPE_NONE
               )
            );

            $input->registerOption(
              new \ezcConsoleOption(
                '',
                'verbose',
                \ezcConsoleInput::TYPE_NONE
               )
            );

            try {
                $input->process();
            }

            catch (\ezcConsoleOptionException $e) {
                print $e->getMessage() . "\n";
                exit(1);
            }

            if ($input->getOption('help')->value) {
                $this->showHelp();
                exit(0);
            }

            else if ($input->getOption('version')->value) {
                $this->printVersionString();
                exit(0);
            }

            $arguments = $input->getArguments();

            if (empty($arguments)) {
                $this->showHelp();
                exit(1);
            }

            $excludes   = $input->getOption('exclude')->value;
            $logPmd     = $input->getOption('log-pmd')->value;
            $minLines   = $input->getOption('min-lines')->value;
            $minTokens  = $input->getOption('min-tokens')->value;
            $names      = explode(',', $input->getOption('names')->value);
            $quiet      = $input->getOption('quiet')->value;
            $verbose    = $input->getOption('verbose')->value;

            array_map('trim', $names);

            if ($input->getOption('progress')->value !== FALSE) {
                $output = new \ezcConsoleOutput;
            } else {
                $output = NULL;
            }

            $this->printVersionString();

            $finder = new FinderFacade($arguments, $excludes, $names);
            $files  = $finder->findFiles();

            if (empty($files)) {
                $this->showError("No files found to scan.\n");
            }

            $strategy = new DefaultStrategy;
            $detector = new Detector($strategy, $output);

            $clones = $detector->copyPasteDetection(
              $files, $minLines, $minTokens
            );

            $printer = new ResultPrinter;
            $printer->printResult($clones, !$quiet, $verbose);
            unset($printer);

            if ($logPmd) {
                $pmd = new PMD($logPmd);
                $pmd->processClones($clones);
                unset($pmd);
            }

            if (count($clones) > 0) {
                exit(1);
            }
        }

        /**
         * Shows an error.
         *
         * @param string $message
         */
        protected function showError($message)
        {
            $this->printVersionString();

            print $message;

            exit(1);
        }

        /**
         * Shows the help.
         */
        protected function showHelp()
        {
            $this->printVersionString();

            print <<<EOT
Usage: phpcpd [switches] <directory|file> ...

  --log-pmd <file>         Write report in PMD-CPD XML format to file.

  --min-lines <N>          Minimum number of identical lines (default: 5).
  --min-tokens <N>         Minimum number of identical tokens (default: 70).

  --exclude <dir>          Exclude <dir> from code analysis.
  --names <names>          A comma-separated list of file names to check.
                           (default: *.php)

  --help                   Prints this usage information.
  --version                Prints the version and exits.

  --progress               Show progress bar.
  --quiet                  Only print the final summary.
  --verbose                Print duplicated code.

EOT;
        }

        /**
         * Prints the version string.
         */
        protected function printVersionString()
        {
            printf(
              "phpcpd %s by Sebastian Bergmann.\n\n", $this->version
            );
        }
    }
}
