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

/**
 * Represents an exact code clone.
 *
 * @since     Class available since Release 1.1.0
 */
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
    private $files = array();

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
        $this->id     = md5($this->getLines());
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
     * @param  string $prefix
     * @return string The lines of the clone
     */
    public function getLines($prefix = '')
    {
        $file = current($this->files);

        if (empty($this->lines)) {
            $lines = array_slice(
                file($file->getName()),
                $file->getStartLine() - 1,
                $this->size
            );

            $indent = array();

            foreach ($lines as &$line) {
                $line    = rtrim($line, " \t\0\x0B");
                $line    = str_replace("\t", '    ', $line);
                $_indent = strlen($line) - strlen(ltrim($line));

                if ($_indent > 1) {
                    $indent[] = $_indent;
                }
            }

            $indent = empty($indent) ? 0 : min($indent);

            if ($indent > 0) {
                foreach ($lines as &$line) {
                    if (strlen($line > 1)) {
                        $line = $prefix . substr($line, $indent);
                    }
                }
            }

            $this->lines = implode('', $lines);
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
