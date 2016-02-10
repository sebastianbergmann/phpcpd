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
use SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\HashStorageFactory;
use SebastianBergmann\PHPCPD\Detector\Adapter\HashStorage\HashStorageInterface;

/**
 * Abstract base class for strategies to detect code clones.
 *
 * @since     Class available since Release 1.4.0
 */
abstract class AbstractStrategy
{
    /**
     * @var integer[] List of tokens to ignore
     */
    protected $tokensIgnoreList = array(
      T_INLINE_HTML        => true,
      T_COMMENT            => true,
      T_DOC_COMMENT        => true,
      T_OPEN_TAG           => true,
      T_OPEN_TAG_WITH_ECHO => true,
      T_CLOSE_TAG          => true,
      T_WHITESPACE         => true,
      T_USE                => true,
      T_NS_SEPARATOR       => true
    );

    /**
     * @var HashStorageInterface
     */
    protected $hashStorageAdapter;

    /**
     *
     * @param HashStorageInterface $hashStorageAdapter
     */
    public function __construct(HashStorageInterface $hashStorageAdapter = null)
    {
        if (null == $hashStorageAdapter) {
            $hashStorageAdapter = HashStorageFactory::createStorageAdapter(null);
        }
        $this->hashStorageAdapter = $hashStorageAdapter;
    }

    /**
     * Copy & Paste Detection (CPD).
     *
     * @param string       $file
     * @param int          $minLines
     * @param int          $minTokens
     * @param CodeCloneMap $result
     * @param bool         $fuzzy
     */
    abstract public function processFile($file, $minLines, $minTokens, CodeCloneMap $result, $fuzzy = false);
}
