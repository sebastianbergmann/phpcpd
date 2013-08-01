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

namespace SebastianBergmann\PHPCPD\Detector;

use SebastianBergmann\PHPCPD\Detector\Strategy\AbstractStrategy;
use SebastianBergmann\PHPCPD\CodeCloneMap;
use Symfony\Component\Console\Helper\ProgressHelper;

/**
 * PHPCPD code analyser.
 *
 * @author    Johann-Peter Hartmann <johann-peter.hartmann@mayflower.de>
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.0.0
 */
class Detector
{
    /**
     * @var SebastianBergmann\PHPCPD\Detector\Strategy\AbstractStrategy
     */
    protected $strategy;

    /**
     * @var Symfony\Component\Console\Helper\ProgressHelper
     */
    protected $progressHelper;

    /**
     * Constructor.
     *
     * @param AbstractStrategy $strategy
     * @since Method available since Release 1.3.0
     */
    public function __construct(AbstractStrategy $strategy, ProgressHelper $progressHelper = null)
    {
        $this->strategy       = $strategy;
        $this->progressHelper = $progressHelper;
    }

    /**
     * Copy & Paste Detection (CPD).
     *
     * @param  Iterator|array $files     List of files to process
     * @param  integer        $minLines  Minimum number of identical lines
     * @param  integer        $minTokens Minimum number of identical tokens
     * @param  boolean        $fuzzy
     * @return CodeCloneMap   Map of exact clones found in the list of files
     */
    public function copyPasteDetection($files, $minLines = 5, $minTokens = 70, $fuzzy = false)
    {
        $result = new CodeCloneMap;

        foreach ($files as $file) {
            $this->strategy->processFile(
                $file,
                $minLines,
                $minTokens,
                $result,
                $fuzzy
            );

            if ($this->progressHelper !== null) {
                $this->progressHelper->advance();
            }
        }

        return $result;
    }
}
