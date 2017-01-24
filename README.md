[![Latest Stable Version](https://img.shields.io/packagist/v/sebastian/phpcpd.svg?style=flat-square)](https://packagist.org/packages/sebastian/phpcpd)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status](https://img.shields.io/travis/sebastianbergmann/phpcpd/master.svg?style=flat-square)](https://travis-ci.org/sebastianbergmann/phpcpd)

# PHP Copy/Paste Detector (PHPCPD)

`phpcpd` is a Copy/Paste Detector (CPD) for PHP code.

## Installation

### PHP Archive (PHAR)

The easiest way to obtain PHPCPD is to download a [PHP Archive (PHAR)](http://php.net/phar) that has all required dependencies of PHPCPD bundled in a single file:

    $ wget https://phar.phpunit.de/phpcpd.phar
    $ chmod +x phpcpd.phar
    $ mv phpcpd.phar /usr/local/bin/phpcpd

You can also immediately use the PHAR after you have downloaded it, of course:

    $ wget https://phar.phpunit.de/phpcpd.phar
    $ php phpcpd.phar

### Composer

You can add this tool as a local, per-project, development-time dependency to your project using [Composer](https://getcomposer.org/):

    $ composer require --dev sebastian/phpcpd

You can then invoke it using the `vendor/bin/phpcpd` executable.

## Usage Example

    $ phpcpd wordpress-4.7.1
    phpcpd 3.0.0 by Sebastian Bergmann.

    Found 59 clones with 2548 duplicated lines in 39 files:

      - /home/sb/wordpress-4.7.1/wp-admin/includes/class-ftp-pure.php:99-114
        /home/sb/wordpress-4.7.1/wp-admin/includes/class-ftp-sockets.php:119-134
      .
      .
      .
      - /home/sb/wordpress-4.7.1/wp-includes/class-wp-customize-manager.php:277-329
        /home/sb/wordpress-4.7.1/wp-includes/class-wp-customize-control.php:652-704

    0.77% duplicated lines out of 332387 total lines of code.

    Time: 2.91 seconds, Memory: 232.00MB

