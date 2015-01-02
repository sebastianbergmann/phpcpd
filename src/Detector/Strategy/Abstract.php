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

use SebastianBergmann\PHPCPD\CodeCloneMap;

/**
 * Abstract base class for strategies to detect code clones.
 *
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.4.0
 */
abstract class AbstractStrategy
{
    /**
     * @var integer[] List of tokens to ignore
     */
    protected $tokensIgnoreList = array(
      T_INLINE_HTML => true,
      T_COMMENT => true,
      T_DOC_COMMENT => true,
      T_OPEN_TAG => true,
      T_OPEN_TAG_WITH_ECHO => true,
      T_CLOSE_TAG => true,
      T_WHITESPACE => true,
      T_USE => true,
      T_NS_SEPARATOR => true
    );

    /**
     * @var string[]
     */
    protected $hashes = array();

    /**
     * Copy & Paste Detection (CPD).
     *
     * @param string       $file
     * @param integer      $minLines
     * @param integer      $minTokens
     * @param CodeCloneMap $result
     * @param boolean      $fuzzy
     */
    abstract public function processFile($file, $minLines, $minTokens, CodeCloneMap $result, $fuzzy = false);
}
