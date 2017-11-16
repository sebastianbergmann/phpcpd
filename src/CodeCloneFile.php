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

class CodeCloneFile
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $startLine;

    /**
     * @param string $name
     * @param int    $startLine
     */
    public function __construct($name, $startLine)
    {
        $this->name      = $name;
        $this->startLine = $startLine;
        $this->id        = $this->name . ':' . $this->startLine;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getStartLine()
    {
        return $this->startLine;
    }
}
