#!/usr/bin/env php
<?php
/*
 * This file is part of StaticReview
 *
 * Copyright (c) 2014 Samuel Parkinson <@samparkinson_>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see http://github.com/sjparkinson/static-review/blob/master/LICENSE.md
 */

require_once realpath(__DIR__ . '/../') . '/vendor/autoload.php';

$args = getopt('', ['hook:']);

$filename = pathinfo(__FILE__, PATHINFO_FILENAME);

if (strpos($_SERVER['SCRIPT_NAME'], 'vendor/bin/' . $filename) !== false
    && isset($args['hook'])) {

    // We're in a Composer included project
    $base = realpath(__DIR__ . '/../../../../');

    echo 'Base Dir: ' . $base . PHP_EOL;

    $hooks = '/vendor/sjparkinson/static-review/hooks';

    $source = $base . $hooks . '/' . $args['hook'] . '.php';
    $target = $base . '/.git/hooks/pre-commit';

    echo 'Source: ' . $source . PHP_EOL;
    echo 'Target: ' . $target . PHP_EOL;

    if (file_exists($source) && ! file_exists($target)) {
        symlink($source, $target);
        chmod($target, 0755);
        echo 'Created Link' . PHP_EOL;
        exit(0);
    }
}

echo 'Something broke! Please add the symlink manually.' . PHP_EOL;

exit(1);
