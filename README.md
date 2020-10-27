# PHP Copy/Paste Detector (PHPCPD)

`phpcpd` is a Copy/Paste Detector (CPD) for PHP code.

## Installation

This tool is distributed as a [PHP Archive (PHAR)](https://php.net/phar):

```bash
$ wget https://phar.phpunit.de/phpcpd.phar

$ php phpcpd.phar --version
```

Using [Phive](https://phar.io/) is the recommended way for managing the tool dependencies of your project:

```bash
$ phive install phpcpd

$ ./tools/phpcpd --version
```

**[It is not recommended to use Composer to download and install this tool.](https://twitter.com/s_bergmann/status/999635212723212288)**

## Usage Example

```
$ php phpcpd.phar --fuzzy wordpress-5.5
phpcpd 6.0.0 by Sebastian Bergmann.

Found 121 clones with 8137 duplicated lines in 69 files:

  - /home/sb/wordpress-5.5/wp-includes/sodium_compat/src/Core/Curve25519/H.php:19-1466 (1447 lines)
    /home/sb/wordpress-5.5/wp-includes/sodium_compat/src/Core32/Curve25519/H.php:19-1466
.
.
.
  - /home/sb/wordpress-5.5/wp-includes/sodium_compat/src/Core32/Curve25519.php:879-889 (10 lines)
    /home/sb/wordpress-5.5/wp-includes/sodium_compat/src/Core32/Curve25519.php:1072-1082

1.82% duplicated lines out of 446676 total lines of code.
Average size of duplication is 67 lines, largest clone has 1447 of lines

Time: 00:02.980, Memory: 318.00 MB
```
