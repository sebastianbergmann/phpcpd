[![Latest Stable Version](https://img.shields.io/packagist/v/sebastian/phpcpd.svg?style=flat-square)](https://packagist.org/packages/sebastian/phpcpd)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status](https://img.shields.io/travis/sebastianbergmann/phpcpd/master.svg?style=flat-square)](https://travis-ci.org/sebastianbergmann/phpcpd)

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

### Run tests

```
$ php phpunit.phar
```

## Usage Example

```
$ phpcpd wordpress-4.7.1
phpcpd 4.0.0-2-ged3cdd4 by Sebastian Bergmann.

Found 60 clones with 2721 duplicated lines in 41 files:

  - /home/sb/WordPress/wp-admin/includes/ajax-actions.php:3680-3710
    /home/sb/WordPress/wp-admin/includes/ajax-actions.php:3968-3998
  .
  .
  .
  - /home/sb/WordPress/wp-includes/rest-api/endpoints/class-wp-rest-terms-controller.php:99-125
    /home/sb/WordPress/wp-includes/rest-api/endpoints/class-wp-rest-users-controller.php:71-97

0.74% duplicated lines out of 366140 total lines of code.
Average size of duplication is 42 lines, biggest clone has 350 of lines
Time: 1.94 seconds, Memory: 272.00MB
```

