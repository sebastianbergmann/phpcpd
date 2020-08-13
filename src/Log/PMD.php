<?php declare(strict_types=1);
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

final class PMD extends AbstractXmlLogger
{
    /** @noinspection UnusedFunctionResultInspection */
    public function processClones(CodeCloneMap $clones): void
    {
        $cpd = $this->document->createElement('pmd-cpd');

        $this->document->appendChild($cpd);

        foreach ($clones as $clone) {
            $duplication = $cpd->appendChild(
                $this->document->createElement('duplication')
            );

            $duplication->setAttribute('lines', (string) $clone->numberOfLines());
            $duplication->setAttribute('tokens', (string) $clone->numberOfTokens());

            foreach ($clone->files() as $codeCloneFile) {
                $file = $duplication->appendChild(
                    $this->document->createElement('file')
                );

                $file->setAttribute('path', $codeCloneFile->name());
                $file->setAttribute('line', (string) $codeCloneFile->startLine());
            }

            $duplication->appendChild(
                $this->document->createElement(
                    'codefragment',
                    $this->escapeForXml($clone->lines())
                )
            );
        }

        $this->flush();
    }
}
