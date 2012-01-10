phpcpd
======

This is the list of changes for the phpcpd 1.3 release series.

phpcpd 1.3.6
------------

* Fixed #18: Wrong result when `mbstring.func_overload` is enabled.

phpcpd 1.3.5
------------

* Fixed #32: `phpcpd.bat` script does not correctly set environment variable without double quotes.
* Fixed #35: `phpcpd` only finds code duplicate that occurs within the same file.

phpcpd 1.3.4
------------

* Fixed #34: `--exclude` option is not processed correctly.

phpcpd 1.3.3
------------

* Added the `--quiet` option to prohibit the output of each found duplicate.

phpcpd 1.3.2
------------

* Use shell exit code `1` when duplicates are found.

phpcpd 1.3.1
------------

* Show an error message when no files are found.
