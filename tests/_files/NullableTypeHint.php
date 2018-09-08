<?php

class NullableTypeHintA
{
    function foo(?string $foo): ?string
    {
        return $foo;
    }
}

class NullableTypeHintB
{
    function foo(string $foo): string
    {
        return $foo;
    }
}