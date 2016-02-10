<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\PHPCPD\Log;

use SebastianBergmann\PHPCPD\CodeCloneMap;

/**
 * Implementation of AbstractXmlLogger that writes in PMD-CPD format.
 *
 * @since     Class available since Release 1.0.0
 */
class PMD extends AbstractXmlLogger
{
    /**
     * Processes a list of clones.
     *
     * @param CodeCloneMap $clones
     */
    public function processClones(CodeCloneMap $clones)
    {
        $cpd = $this->document->createElement('pmd-cpd');
        $this->document->appendChild($cpd);

        foreach ($clones as $clone) {
            $duplication = $cpd->appendChild(
                $this->document->createElement('duplication')
            );

            $duplication->setAttribute('lines', $clone->getSize());
            $duplication->setAttribute('tokens', $clone->getTokens());

            foreach ($clone->getFiles() as $codeCloneFile) {
                $file = $duplication->appendChild(
                    $this->document->createElement('file')
                );

                $file->setAttribute('path', $codeCloneFile->getName());
                $file->setAttribute('line', $codeCloneFile->getStartLine());
            }

            $duplication->appendChild(
                $this->document->createElement(
                    'codefragment',
                    htmlspecialchars(
                        $this->convertToUtf8($clone->getLines()),
                        ENT_COMPAT,
                        'UTF-8'
                    )
                )
            );
        }

        $this->flush();
    }
}
