<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\Detector\Strategy;

use function is_array;
use function array_keys;
use function file_get_contents;
use function token_get_all;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\PhpToken;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\CloneInfo;

final class SuffixTreeStrategy
{
    public function processFile(string $file, int $minLines, int $minTokens, CodeCloneMap $result, bool $fuzzy = false): void
    {
        $content = file_get_contents($file);
        $tokens = token_get_all($content);
        $word = [];

        foreach (array_keys($tokens) as $key) {
            $token = $tokens[$key];

            if (is_array($token)) {
                if (!isset($this->tokensIgnoreList[$token[0]])) {
                    $word[] = new PhpToken(
                        $token[0],
                        token_name($token[0]),
                        $token[2],
                        $file,
                        $token[1]
                    );
                }
            }
        }
        $tree = new ApproximateCloneDetectingSuffixTree($word);
        /** @var CloneInfo[] */
        $cloneInfos = $tree->findClones(10, 5, 10);
    }
}
