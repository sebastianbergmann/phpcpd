<?php

namespace SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree;

class PhpToken implements JavaObjectInterface
{
    public $tokenCode;
    public $line;
    public $file;
    public $tokenName;
    public $content;

    public function __construct(
        int $tokenCode,
        string $tokenName,
        int $line,
        string $file,
        string $content
    ) {
        $this->tokenCode = $tokenCode;
        $this->tokenName = $tokenName;
        $this->line = $line;
        $this->content = $content;
        $this->file = $file;
    }

    /**
     * @return int
     */
    public function hashCode(): int
    {
        return (int) crc32($this->content);

        //static $cashedHashCode = null;
        //if ($cashedHashCode !== null) {
            //return $cashedHashCode;
        //}

        // Code below mimics 32-bit integer. Probably not needed.
        /*
        $value = $this->content;
        $hashCode = 0;
        $offset= 0;
        $limit = strlen($value) + $offset;
        for ($i = $offset; $i < $limit; $i++) {
            $hashCode = $hashCode * 31 + ord($value[$i]);
            //if (is_float($hashCode)) {
                //die('nooo');
            //}
            // NB: Simulate 32-bit int.
            // @see https://stackoverflow.com/questions/15557407/how-to-use-a-32bit-integer-on-a-64bit-installation-of-php
            //$hashCode = $hashCode & 0xFFFFFFFF;
            $hashCode = $hashCode & 0xFFFFFFFF;
            if ($hashCode & 0x80000000) {
                $hashCode = $hashCode & ~0x80000000;
                $hashCode = -2147483648 + $hashCode;
            }

        }
        //$cashedHashCode = $hashCode;
        return $hashCode;
         */
        //return $this->content->hashCode();
        //return $tokenCode;
    }

    /**
     * @return boolean
     */
    public function equals(JavaObjectInterface $token): bool {
        return $token->hashCode() === $this->hashCode();
    }

    /**
     * @return string
     */
    public function toString() {
        return $this->tokenName;
    }

    public function __tostring() {
        return $this->tokenName;
    }
}
