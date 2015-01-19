Static-Review
=============

[![Latest Stable Version][icon-version][packagist]
[![Build Status][icon-build-status]][travis]
[![Minimum PHP Version][icon-php-version][php]

[packagist]: https://packagist.org/packages/sjparkinson/static-review
[travis]:    https://travis-ci.org/sjparkinson/static-review
[php]:       https://php.net/

[icon-version]: http://img.shields.io/packagist/v/sjparkinson/static-review.svg?style=flat
[icon-build-status]: http://img.shields.io/travis/sjparkinson/static-review/master.svg?style=flat
[icon-php-version]: http://img.shields.io/badge/php-~5.4-8892BF.svg?style=flat

An extendible framework for version control hooks.

![StaticReview Success Demo](https://i.imgur.com/8G3uORp.gif)

## Usage

For a [composer](https://getcomposer.org) managed project you can simply run the following ...

```bash
$ composer require sjparkinson/static-review
```

Hooks can then be installed like so ...

```bash
$ vendor/bin/static-review.php hook:install vendor/sjparkinson/static-review/hooks/example-pre-commit.php .git/hooks/pre-commit
```

## Example Hook

Below is a basic hook that you can extend upon.

```php
#!/usr/bin/env php
<?php

include __DIR__ . '/../../../autoload.php';

// Reference the required classes.
use StaticReview\StaticReview;
[...]

$reporter = new Reporter();
$review   = new StaticReview($reporter);

// Add any reviews to the StaticReview instance, supports a fluent interface.
$review->addReview(new LineEndingsReview());

$git = new GitVersionControl();

// Review the staged files.
$review->review($git->getStagedFiles());

// Check if any issues were found.
// Exit with a non-zero status to block the commit.
($reporter->hasIssues()) ? exit(1) : exit(0);
```

## Example Review

```php
class NoCommitTagReview extends AbstractReview
{
    // Review any text based file.
    public function canReview(FileInterface $file)
    {
        $mime = $file->getMimeType();

        // check to see if the mime-type starts with 'text'
        return (substr($mime, 0, 4) === 'text');
    }

    // Checks if the file contains `NOCOMMIT`.
    public function review(ReporterInterface $reporter, FileInterface $file)
    {
        $cmd = sprintf('grep --fixed-strings --ignore-case --quiet "NOCOMMIT" %s', $file->getFullPath());

        $process = $this->getProcess($cmd);
        $process->run();

        if ($process->isSuccessful()) {
            $message = 'A NOCOMMIT tag was found';
            $reporter->error($message, $this, $file);
        }
    }
}
```

## Unit Tests

See [phpunit.de][phpunit].

```bash
$ git clone https://github.com/sjparkinson/static-review.git
$ cd static-review/
$ composer install
$ composer run-script test
```

[phpunit]: http://phpunit.de

## Licence

The content of this library is released under the [MIT License][license] by [Samuel Parkinson][twitter].

[license]: https://github.com/sjparkinson/static-review/blob/master/LICENSE
[twitter]: https://twitter.com/samparkinson_
