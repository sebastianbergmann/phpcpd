phpcpd
======

This is the list of changes for the phpcpd 1.4 release series.

phpcpd 1.4.0
------------

* The `--verbose` switch now enables printing of the duplicated code lines.
* The progress bar is no longer enabled with `--verbose` but with `--progress`.
* Fixed #4: Incorrect reporting of duplicated lines when duplication occurs near end of tokens.
* Fixed #18: Wrong result when `mbstring.func_overload` is enabled.
