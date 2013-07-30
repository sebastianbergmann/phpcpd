phpcpd
======

This is the list of changes for the phpcpd 1.4 release series.

phpcpd 1.4.2
------------

* Upgraded bundled Version component for PHAR distribution.

phpcpd 1.4.2
------------

* Fixed `composer.json`.

phpcpd 1.4.1
------------

* The [Version](http://github.com/sebastianbergmann/version) component is now used to manage the version number.

phpcpd 1.4.0
------------

* The `--verbose` switch now enables printing of the duplicated code lines.
* The progress bar is no longer enabled with `--verbose` but with `--progress`.
* The [Finder](http://symfony.com/doc/2.0/components/finder.html) component of the Symfony project is now used to find files.
* Fixed #4: Incorrect reporting of duplicated lines when duplication occurs near end of tokens.
* Fixed #18: Wrong result when `mbstring.func_overload` is enabled.
* PHP 5.3.3 is now required to use PHPCPD.
