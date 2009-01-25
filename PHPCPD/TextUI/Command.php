<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009, Sebastian Bergmann <sb@sebastian-bergmann.de>.
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
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @since     File available since Release 1.0.0
 */

require 'PHPCPD/Detector.php';
require 'PHPCPD/TextUI/Getopt.php';
require 'PHPCPD/TextUI/ResultPrinter.php';
require 'PHPCPD/Util/FilterIterator.php';
require 'PHPCPD/Log/XML/PMD.php';

/**
 *
 *
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @copyright 2009 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.0.0
 */
class PHPCPD_TextUI_Command
{
    public static function main()
    {
        try {
            $options = PHPCPD_TextUI_Getopt::getopt(
              $_SERVER['argv'],
              '',
              array(
                'help',
                'log-pmd=',
                'min-lines=',
                'min-tokens=',
                'suffixes=',
                'version'
              )
            );
        }

        catch (RuntimeException $e) {
            self::showError($e->getMessage());
        }

        $minLines  = 5;
        $minTokens = 70;
        $suffixes  = array('php');

        foreach ($options[0] as $option) {
            switch ($option[0]) {
                case '--log-pmd': {
                    $logPmd = $option[1];
                }
                break;

                case '--min-lines': {
                    if (is_int($option[1])) {
                        $minLines = (int)$option[1];
                    }
                }
                break;

                case '--min-tokens': {
                    if (is_int($option[1])) {
                        $minTokens = (int)$option[1];
                    }
                }
                break;

                case '--suffixes': {
                    $suffixes = explode(',', $option[1]);
                    array_map('trim', $suffixes);
                }
                break;

                case '--help': {
                    self::showHelp();
                    exit(0);
                }
                break;

                case '--version': {
                    self::printVersionString();
                    exit(0);
                }
                break;
            }
        }

        if (isset($options[1][0])) {
            if (is_dir($options[1][0])) {
                $files = new PHPCPD_Util_FilterIterator(
                  new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($options[1][0])
                  ),
                  $suffixes
                );
            }

            else if (is_file($options[1][0])) {
                $files = array(new SPLFileInfo($options[1][0]));
            }
        }

        if (!isset($files)) {
            self::showHelp();
            exit(1);
        }

        self::printVersionString();

        $duplicates = PHPCPD_Detector::copyPasteDetection(
          $files, $minLines, $minTokens
        );

        $printer = new PHPCPD_TextUI_ResultPrinter;
        $printer->printResult($duplicates);
        unset($printer);

        if (isset($logPmd)) {
            $pmd = new PHPCPD_Log_XML_PMD($logPmd);
            $pmd->processDuplicates($duplicates);
            unset($pmd);
        }
    }

    /**
     * Shows an error.
     */
    protected static function showError($message)
    {
        self::printVersionString();

        print $message;

        exit(1);
    }

    /**
     * Shows the help.
     */
    protected static function showHelp()
    {
        self::printVersionString();

        print <<<EOT
Usage: phpcpd [switches] <directory>
       phpcpd [switches] <file>

  --log-pmd <file>         Write report in PMD-CPD XML format to file.

  --min-lines <N>          Minimum number of lines for a duplicate code block.
  --min-tokens <N>         Minimum number of tokens for a duplicate code block.

  --suffixes <suffix,...>  A comma-separated list of file suffixes to check.

  --help                   Prints this usage information.
  --version                Prints the version and exits.

EOT;
    }

    /**
     * Prints the version string.
     */
    protected static function printVersionString()
    {
        print "phpcpd @package_version@ by Sebastian Bergmann.\n\n";
    }
}
?>
