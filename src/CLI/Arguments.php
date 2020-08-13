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

final class Arguments
{
    /**
     * @psalm-var list<string>
     */
    private $directories;

    /**
     * @psalm-var list<string>
     */
    private $suffixes;

    /**
     * @psalm-var list<string>
     */
    private $exclude;

    /**
     * @var ?string
     */
    private $pmdCpdXmlLogfile;

    /**
     * @var int
     */
    private $linesThreshold;

    /**
     * @var int
     */
    private $tokensThreshold;

    /**
     * @var bool
     */
    private $fuzzy;

    /**
     * @var bool
     */
    private $verbose;

    /**
     * @var bool
     */
    private $help;

    /**
     * @var bool
     */
    private $version;

    public function __construct(array $directories, array $suffixes, array $exclude, ?string $pmdCpdXmlLogfile, int $linesThreshold, int $tokensThreshold, bool $fuzzy, bool $verbose, bool $help, bool $version)
    {
        $this->directories      = $directories;
        $this->suffixes         = $suffixes;
        $this->exclude          = $exclude;
        $this->pmdCpdXmlLogfile = $pmdCpdXmlLogfile;
        $this->linesThreshold   = $linesThreshold;
        $this->tokensThreshold  = $tokensThreshold;
        $this->fuzzy            = $fuzzy;
        $this->verbose          = $verbose;
        $this->help             = $help;
        $this->version          = $version;
    }

    /**
     * @psalm-return list<string>
     */
    public function directories(): array
    {
        return $this->directories;
    }

    /**
     * @psalm-return list<string>
     */
    public function suffixes(): array
    {
        return $this->suffixes;
    }

    /**
     * @psalm-return list<string>
     */
    public function exclude(): array
    {
        return $this->exclude;
    }

    public function pmdCpdXmlLogfile(): ?string
    {
        return $this->pmdCpdXmlLogfile;
    }

    public function linesThreshold(): int
    {
        return $this->linesThreshold;
    }

    public function tokensThreshold(): int
    {
        return $this->tokensThreshold;
    }

    public function fuzzy(): bool
    {
        return $this->fuzzy;
    }

    public function verbose(): bool
    {
        return $this->verbose;
    }

    public function help(): bool
    {
        return $this->help;
    }

    public function version(): bool
    {
        return $this->version;
    }
}
