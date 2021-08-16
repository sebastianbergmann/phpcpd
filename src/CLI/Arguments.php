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
    private array $directories;

    /**
     * @psalm-var list<string>
     */
    private array $suffixes;

    /**
     * @psalm-var list<string>
     */
    private array $exclude;

    private ?string $pmdCpdXmlLogfile;

    private int $linesThreshold;

    private int $tokensThreshold;

    private bool $fuzzy;

    private bool $verbose;

    private bool $help;

    private bool $version;

    private ?string $algorithm;

    private int $editDistance;

    private int $headEquality;

    public function __construct(array $directories, array $suffixes, array $exclude, ?string $pmdCpdXmlLogfile, int $linesThreshold, int $tokensThreshold, bool $fuzzy, bool $verbose, bool $help, bool $version, ?string $algorithm, int $editDistance, int $headEquality)
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
        $this->algorithm        = $algorithm;
        $this->editDistance     = $editDistance;
        $this->headEquality     = $headEquality;
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

    public function algorithm(): ?string
    {
        return $this->algorithm;
    }

    public function editDistance(): int
    {
        return $this->editDistance;
    }

    public function headEquality(): int
    {
        return $this->headEquality;
    }
}
