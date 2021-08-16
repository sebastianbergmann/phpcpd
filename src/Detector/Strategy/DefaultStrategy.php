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

use const T_VARIABLE;
use function array_keys;
use function chr;
use function count;
use function crc32;
use function file_get_contents;
use function is_array;
use function md5;
use function pack;
use function substr;
use function substr_count;
use function token_get_all;
use SebastianBergmann\PHPCPD\CodeClone;
use SebastianBergmann\PHPCPD\CodeCloneFile;
use SebastianBergmann\PHPCPD\CodeCloneMap;

/**
 *  This is a Rabin-Karp with an additional normalization steps before
 *  the hashing happens.
 *
 *  1. Tokenization
 *  2. Deletion of logic neutral tokens like T_CLOSE_TAG;T_COMMENT;
 *      T_DOC_COMMENT; T_INLINE_HTML; T_NS_SEPARATOR; T_OPEN_TAG;
 *      T_OPEN_TAG_WITH_ECHO; T_USE; T_WHITESPACE;
 *  3. If needed deletion of variable names
 *  4. Normalization of token + value using crc32
 *  5. Now the classic Rabin-Karp hashing takes place
 */
final class DefaultStrategy extends AbstractStrategy
{
    /**
     * @psalm-var array<string,array{0: string, 1: int}>
     */
    private array $hashes = [];

    public function processFile(string $file, CodeCloneMap $result): void
    {
        $buffer                    = file_get_contents($file);
        $currentTokenPositions     = [];
        $currentTokenRealPositions = [];
        $currentSignature          = '';
        $tokens                    = token_get_all($buffer);
        $tokenNr                   = 0;
        $lastTokenLine             = 0;

        $result->addToNumberOfLines(substr_count($buffer, "\n"));

        unset($buffer);

        foreach (array_keys($tokens) as $key) {
            $token = $tokens[$key];

            if (is_array($token)) {
                if (!isset($this->tokensIgnoreList[$token[0]])) {
                    if ($tokenNr === 0) {
                        $currentTokenPositions[$tokenNr] = $token[2] - $lastTokenLine;
                    } else {
                        $currentTokenPositions[$tokenNr] = $currentTokenPositions[$tokenNr - 1] +
                                                           $token[2] - $lastTokenLine;
                    }

                    $currentTokenRealPositions[$tokenNr++] = $token[2];

                    if ($this->config->fuzzy() && $token[0] === T_VARIABLE) {
                        $token[1] = 'variable';
                    }

                    $currentSignature .= chr($token[0] & 255) .
                                         pack('N*', crc32($token[1]));
                }

                $lastTokenLine = $token[2];
            }
        }

        $count         = count($currentTokenPositions);
        $firstLine     = 0;
        $firstRealLine = 0;
        $found         = false;
        $tokenNr       = 0;

        while ($tokenNr <= $count - $this->config->minTokens()) {
            $line     = $currentTokenPositions[$tokenNr];
            $realLine = $currentTokenRealPositions[$tokenNr];

            $hash = substr(
                md5(
                    substr(
                        $currentSignature,
                        $tokenNr * 5,
                        $this->config->minTokens() * 5
                    ),
                    true
                ),
                0,
                8
            );

            if (isset($this->hashes[$hash])) {
                $found = true;

                if ($firstLine === 0) {
                    $firstLine     = $line;
                    $firstRealLine = $realLine;
                    $firstHash     = $hash;
                    $firstToken    = $tokenNr;
                }
            } else {
                if ($found) {
                    $fileA        = $this->hashes[$firstHash][0];
                    $firstLineA   = $this->hashes[$firstHash][1];
                    $lastToken    = ($tokenNr - 1) + $this->config->minTokens() - 1;
                    $lastLine     = $currentTokenPositions[$lastToken];
                    $lastRealLine = $currentTokenRealPositions[$lastToken];
                    $numLines     = $lastLine + 1 - $firstLine;
                    $realNumLines = $lastRealLine + 1 - $firstRealLine;

                    if ($numLines >= $this->config->minLines() &&
                        ($fileA !== $file ||
                         $firstLineA !== $firstRealLine)) {
                        $result->add(
                            new CodeClone(
                                new CodeCloneFile($fileA, $firstLineA),
                                new CodeCloneFile($file, $firstRealLine),
                                $realNumLines,
                                $lastToken + 1 - $firstToken
                            )
                        );
                    }

                    $found     = false;
                    $firstLine = 0;
                }

                $this->hashes[$hash] = [$file, $realLine];
            }

            $tokenNr++;
        }

        if ($found) {
            $fileA        = $this->hashes[$firstHash][0];
            $firstLineA   = $this->hashes[$firstHash][1];
            $lastToken    = ($tokenNr - 1) + $this->config->minTokens() - 1;
            $lastLine     = $currentTokenPositions[$lastToken];
            $lastRealLine = $currentTokenRealPositions[$lastToken];
            $numLines     = $lastLine + 1 - $firstLine;
            $realNumLines = $lastRealLine + 1 - $firstRealLine;

            if ($numLines >= $this->config->minLines() &&
                ($fileA !== $file || $firstLineA !== $firstRealLine)) {
                $result->add(
                    new CodeClone(
                        new CodeCloneFile($fileA, $firstLineA),
                        new CodeCloneFile($file, $firstRealLine),
                        $realNumLines,
                        $lastToken + 1 - $firstToken
                    )
                );
            }
        }
    }
}
