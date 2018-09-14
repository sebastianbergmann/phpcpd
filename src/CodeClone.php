<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD;

final class CodeClone
{
    /**
     * @var int Size of the clone (lines)
     */
    private $size;

    /**
     * @var int Size of the clone (tokens)
     */
    private $tokens;

    /**
     * @var CodeCloneFile[] Files with this code clone
     */
    private $files = [];

    /**
     * @var string Unique ID of Code Duplicate Fragment
     */
    private $id;

    /**
     * @var string Lines of the clone
     */
    private $lines = '';

    public function __construct(CodeCloneFile $fileA, CodeCloneFile $fileB, int $size, int $tokens)
    {
        $this->addFile($fileA);
        $this->addFile($fileB);

        $this->size   = $size;
        $this->tokens = $tokens;
        $this->id     = \md5($this->getLines());
    }

    public function addFile(CodeCloneFile $file): void
    {
        $id = $file->getId();

        if (!isset($this->files[$id])) {
            $this->files[$id] = $file;
        }
    }

    /**
     * @return CodeCloneFile[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    public function getLines($indent = ''): string
    {
        if (empty($this->lines)) {
            $file = \current($this->files);

            $this->lines = \implode(
                '',
                \array_map(
                    function ($line) use ($indent) {
                        return $indent . $line;
                    },
                    \array_slice(
                        \file($file->getName()),
                        $file->getStartLine() - 1,
                        $this->size
                    )
                )
            );
        }

        return $this->lines;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getTokens(): int
    {
        return $this->tokens;
    }
}
