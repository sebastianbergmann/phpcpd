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

class CodeClone
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

    /**
     * Constructor.
     *
     * @param CodeCloneFile $fileA
     * @param CodeCloneFile $fileB
     * @param int           $size
     * @param int           $tokens
     */
    public function __construct(CodeCloneFile $fileA, CodeCloneFile $fileB, $size, $tokens)
    {
        $this->addFile($fileA);
        $this->addFile($fileB);

        $this->size   = $size;
        $this->tokens = $tokens;
        $this->id     = \md5($this->getLines());
    }

    /**
     * Add file with clone
     *
     * @param CodeCloneFile $file
     */
    public function addFile(CodeCloneFile $file)
    {
        $id = $file->getId();

        if (!isset($this->files[$id])) {
            $this->files[$id] = $file;
        }
    }

    /**
     * Get files with clone
     *
     * @return CodeCloneFile[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Returns the lines of the clone.
     *
     * @param string $indent
     *
     * @return string The lines of the clone
     */
    public function getLines($indent = '')
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

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getTokens()
    {
        return $this->tokens;
    }
}
