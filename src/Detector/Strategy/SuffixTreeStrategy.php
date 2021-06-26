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
use Exception;
use SebastianBergmann\PHPCPD\CodeClone;
use SebastianBergmann\PHPCPD\CodeCloneFile;
use SebastianBergmann\PHPCPD\CodeCloneMap;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\AbstractToken;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\ApproximateCloneDetectingSuffixTree;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\Sentinel;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\Token;

final class SuffixTreeStrategy extends AbstractStrategy
{
    /**
     * @var AbstractToken[]
     */
    private $word = [];

    /**
     * @var ?CodeCloneMap
     */
    private $result;

    public function processFile(string $file, CodeCloneMap $result): void
    {
        $content = file_get_contents($file);
        $tokens  = token_get_all($content);

        foreach (array_keys($tokens) as $key) {
            $token = $tokens[$key];

            if (is_array($token)) {
                if (!isset($this->tokensIgnoreList[$token[0]])) {
                    $this->word[] = new Token(
                        $token[0],
                        token_name($token[0]),
                        $token[2],
                        $file,
                        $token[1]
                    );
                }
            }
        }

        $this->result = $result;
    }

    public function postProcess(): void
    {
        if (empty($this->result)) {
            throw new Exception('Missing result');
        }

        // Sentinel = End of word
        $this->word[] = new Sentinel();

        $tree       = new ApproximateCloneDetectingSuffixTree($this->word);
        $cloneInfos = $tree->findClones(
            $this->config->getMinTokens(),
            $this->config->getEditDistance(),
            $this->config->getHeadEquality()
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
