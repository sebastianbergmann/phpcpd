[![Latest Stable Version](https://img.shields.io/packagist/v/sebastian/phpcpd.svg?style=flat-square)](https://packagist.org/packages/sebastian/phpcpd)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg?style=flat-square)](https://php.net/)

# PHP Copy/Paste Detector (PHPCPD)

`phpcpd` is a Copy/Paste Detector (CPD) for PHP code.

## Installation

### PHP Archive (PHAR)

The easiest way to obtain PHPCPD is to download a [PHP Archive (PHAR)](http://php.net/phar) that has all required dependencies of PHPCPD bundled in a single file:

```
$ wget https://phar.phpunit.de/phpcpd.phar
$ chmod +x phpcpd.phar
$ mv phpcpd.phar /usr/local/bin/phpcpd
```

You can also immediately use the PHAR after you have downloaded it, of course:

```
$ wget https://phar.phpunit.de/phpcpd.phar
$ php phpcpd.phar
```

### Composer

You can add this tool as a local, per-project, development-time dependency to your project using [Composer](https://getcomposer.org/):

```
$ composer require --dev sebastian/phpcpd
```

You can then invoke it using the `vendor/bin/phpcpd` executable.

## Usage Example

```
$ phpcpd --fuzzy wordpress-4.9.8
phpcpd 5.0.0 by Sebastian Bergmann.

Found 66 clones with 3014 duplicated lines in 40 files:

  - /home/sb/wordpress-4.9.8/wp-includes/Requests/IRI.php:358-708 (350 lines)
    /home/sb/wordpress-4.9.8/wp-includes/SimplePie/IRI.php:404-754
.
.
.
  - /home/sb/wordpress-4.9.8/wp-includes/SimplePie/File.php:133-144 (11 lines)
    /home/sb/wordpress-4.9.8/wp-includes/SimplePie/File.php:215-226

0.86% duplicated lines out of 349460 total lines of code.
Average size of duplication is 45 lines, largest clone has 350 of lines

Time: 1.79 seconds, Memory: 272.00MB
```

