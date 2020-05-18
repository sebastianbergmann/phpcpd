# PHP Copy/Paste Detector (PHPCPD)

`phpcpd` is a Copy/Paste Detector (CPD) for PHP code.

## Installation

This tool is distributed as a [PHP Archive (PHAR)](https://php.net/phar):

```bash
$ wget https://phar.phpunit.de/phpcpd.phar

$ php phpcpd.phar --version
```

Using [Phive](https://phar.io/) is the recommended way for managing the tool dependencies of your project.

**[It is not recommended to use Composer to download and install this tool.](https://twitter.com/s_bergmann/status/999635212723212288)**

## Usage Example

```
$ php phpcpd.phar --fuzzy wordpress-4.9.8
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
