<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\Detector\Strategy;

use function array_keys;
use function file_get_contents;
use function is_array;
use function token_get_all;
use SebastianBergmann\PHPCPD\CodeClone;
use SebastianBergmann\PHPCPD\CodeCloneFile;
use SebastianBergmann\PHPCPD\CodeCloneMap;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\AbstractToken;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\ApproximateCloneDetectingSuffixTree;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\Sentinel;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\Token;
use SebastianBergmann\PHPCPD\MissingResultException;

/**
 * The suffix tree strategy was implemented in PHP for PHPCPD by Olle Härstedt.
 *
 * This PHP implementation is based on the Java implementation archived that is
 * available at https://www.cqse.eu/en/news/blog/conqat-end-of-life/ under the
 * Apache License 2.0.
 *
 * The aforementioned Java implementation is based on the algorithm described in
 * https://dl.acm.org/doi/10.1109/ICSE.2009.5070547. This paper is available at
 * https://www.cqse.eu/fileadmin/content/news/publications/2009-do-code-clones-matter.pdf.
 */
final class SuffixTreeStrategy extends AbstractStrategy
{
    /**
     * @psalm-var list<AbstractToken>
     */
    private array $word = [];

    private ?CodeCloneMap $result = null;

    public function processFile(string $file, CodeCloneMap $result): void
    {
        $content = file_get_contents($file);
        $tokens  = token_get_all($content);

        foreach (array_keys($tokens) as $key) {
            $token = $tokens[$key];

            if (is_array($token) && !isset($this->tokensIgnoreList[$token[0]])) {
                $this->word[] = new Token(
                    $token[0],
                    token_name($token[0]),
                    $token[2],
                    $file,
                    $token[1]
                );
            }
        }

        $this->result = $result;
    }

    /**
     * @throws MissingResultException
     */
    public function postProcess(): void
    {
        if (empty($this->result)) {
            throw new MissingResultException('Missing result');
        }

        // Sentinel = End of word
        $this->word[] = new Sentinel;

        $cloneInfos = (new ApproximateCloneDetectingSuffixTree($this->word))->findClones(
            $this->config->minTokens(),
            $this->config->editDistance(),
            $this->config->headEquality()
        );

        foreach ($cloneInfos as $cloneInfo) {
            /** @var int[] */
            $others = $cloneInfo->otherClones->extractFirstList();

            for ($j = 0; $j < count($others); $j++) {
                $otherStart = $others[$j];
                $t          = $this->word[$otherStart];
                $lastToken  = $this->word[$cloneInfo->position + $cloneInfo->length];
                // If we stumbled upon the Sentinel, rewind one step.
                if ($lastToken instanceof Sentinel) {
                    $lastToken = $this->word[$cloneInfo->position + $cloneInfo->length - 2];
                }
                $lines = $lastToken->line - $cloneInfo->token->line;
                $this->result->add(
                    new CodeClone(
                        new CodeCloneFile($cloneInfo->token->file, $cloneInfo->token->line),
                        new CodeCloneFile($t->file, $t->line),
                        $lines,
                        // TODO: Double check this
                        $otherStart + 1 - $cloneInfo->position
                    )
                );
            }
        }
    }
}
