<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD;

use function array_map;
use function array_slice;
use function current;
use function file;
use function implode;
use function md5;

final class CodeClone
{
    /**
     * @var int
     */
    private $numberOfLines;

    /**
     * @var int
     */
    private $numberOfTokens;

    /**
     * @var CodeCloneFile[]
     */
    private $files = [];

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $lines = '';

    public function __construct(CodeCloneFile $fileA, CodeCloneFile $fileB, int $numberOfLines, int $numberOfTokens)
    {
        $this->add($fileA);
        $this->add($fileB);

        $this->numberOfLines  = $numberOfLines;
        $this->numberOfTokens = $numberOfTokens;
        $this->id             = md5($this->lines());
    }

    public function add(CodeCloneFile $file): void
    {
        $id = $file->id();

        if (!isset($this->files[$id])) {
            $this->files[$id] = $file;
        }
    }

    /**
     * @return CodeCloneFile[]
     */
    public function files(): array
    {
        return $this->files;
    }

    public function lines($indent = ''): string
    {
        if (empty($this->lines)) {
            $file = current($this->files);

            $this->lines = implode(
                '',
                array_map(
                    function ($line) use ($indent) {
                        return $indent . $line;
                    },
                    array_slice(
                        file($file->name()),
                        $file->startLine() - 1,
                        $this->numberOfLines
                    )
                )
            );
        }

        return $this->lines;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function numberOfLines(): int
    {
        return $this->numberOfLines;
    }

    public function numberOfTokens(): int
    {
        return $this->numberOfTokens;
    }
}
