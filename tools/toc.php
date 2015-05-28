<?php error_reporting(E_ALL);

$dir = __DIR__ . '/../spec/';
$tocFile = $dir . '00-specification-for-php.md';
$prefix = <<<EOS
<!-- This file is autogenerated, do not edit it manually -->
<!-- Run tools/toc.php instead -->

#Specification for PHP
Facebook has dedicated all copyright to this specification to the public
domain worldwide under the CC0 Public Domain Dedication located at
<http://creativecommons.org/publicdomain/zero/1.0/>. This specification
is distributed without any warranty.

(Initially written in 2014 by Facebook, Inc., July 2014)

**Table of Contents** 
EOS;

$files = scandir($dir);
$output = "";

foreach ($files as $file) {
    if(pathinfo($file, PATHINFO_EXTENSION) != 'md') {
        continue;
    }
    if ($file == '00-specification-for-php.md' || $file == 'php-spec-draft.md') {
        continue;
    }

    $anchors = [];

    $lines = file($dir . $file);
    foreach ($lines as $line) {
        if (!preg_match('/^(#+)\s*(.+)/', $line, $matches)) {
            continue;
        }

        list(, $hashes, $title) = $matches;
        $level = strlen($hashes) - 1;
        $indent = str_repeat('  ', $level);

        $anchor = strtr(strtolower($title), ' ', '-');
        $anchor = preg_replace('/[^\w-]/', '', $anchor);

        if (isset($anchors[$anchor])) {
            $anchors[$anchor]++;
            $anchor .= '-' . $anchors[$anchor];
        } else {
            $anchors[$anchor] = 0;
        }

        $output .= "$indent- [$title]($file#$anchor)\n";
    }
}

file_put_contents($tocFile, "$prefix\n$output");

