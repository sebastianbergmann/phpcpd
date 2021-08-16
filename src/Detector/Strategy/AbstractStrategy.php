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

use const T_CLOSE_TAG;
use const T_COMMENT;
use const T_DOC_COMMENT;
use const T_INLINE_HTML;
use const T_NS_SEPARATOR;
use const T_OPEN_TAG;
use const T_OPEN_TAG_WITH_ECHO;
use const T_USE;
use const T_WHITESPACE;
use SebastianBergmann\PHPCPD\CodeCloneMap;

abstract class AbstractStrategy
{
    /**
     * @psalm-var array<int,true>
     */
    protected array $tokensIgnoreList = [
        T_INLINE_HTML        => true,
        T_COMMENT            => true,
        T_DOC_COMMENT        => true,
        T_OPEN_TAG           => true,
        T_OPEN_TAG_WITH_ECHO => true,
        T_CLOSE_TAG          => true,
        T_WHITESPACE         => true,
        T_USE                => true,
        T_NS_SEPARATOR       => true,
    ];

    protected StrategyConfiguration $config;

    public function __construct(StrategyConfiguration $config)
    {
        $this->config = $config;
    }

    public function setConfig(StrategyConfiguration $config): void
    {
        $this->config = $config;
    }

    abstract public function processFile(string $file, CodeCloneMap $result): void;

    public function postProcess(): void
    {
    }
}
