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

require 'PHPCPD/Log/XML.php';

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
class PHPCPD_Log_XML_PMD extends PHPCPD_Log_XML
{
    public function processDuplicates(array $duplicates)
    {
        $cpd = $this->document->createElement('pmd-cpd');
        $cpd->setAttribute('version', 'phpcpd @package_version@');
        $this->document->appendChild($cpd);

        foreach ($duplicates as $duplicate) {
            $duplication = $cpd->appendChild(
              $this->document->createElement('duplication')
            );

            $duplication->setAttribute('lines', $duplicate['numLines']);
            $duplication->setAttribute('tokens', $duplicate['numTokens']);

            $file = $duplication->appendChild(
              $this->document->createElement('file')
            );

            $file->setAttribute('path', $duplicate['fileA']);
            $file->setAttribute('line', $duplicate['firstLineA']);

            $file = $duplication->appendChild(
              $this->document->createElement('file')
            );

            $file->setAttribute('path', $duplicate['fileB']);
            $file->setAttribute('line', $duplicate['firstLineB']);

            $duplication->appendChild(
              $this->document->createElement(
                'codefragment',
                htmlspecialchars(
                  $this->convertToUtf8(
                    join(
                      '',
                      array_slice(
                        file($duplicate['fileA']),
                        $duplicate['firstLineA'] - 1,
                        $duplicate['numLines']
                      )
                    )
                  ),
                  ENT_COMPAT,
                  'UTF-8'
                )
              )
            );
        }

        $this->flush();
    }
}
?>
