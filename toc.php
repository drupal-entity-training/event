<?php

declare(strict_types=1);

$file = file('index.md');
$headers = array_filter($file, function (string $line) {
  return substr($line, 0, 3) === '###';
});

$h3 = 0;
$h4 = 0;
foreach ($headers as $header) {
  list(, $title) = explode(' ', rtrim($header), 2);

  if ($header[3] !== '#') {
    ++$h3;
    $h4 = 0;
  }
  else {
    ++$h4;
  }

  $link = '[' . $title . '](#' . strtolower(str_replace(' ', '-', $title)) . ')';
  if ($header[3] !== '#') {
    print $h3 . '. ' . $link . PHP_EOL;
  }
  else {
    print '      ' . $h4 . '. ' . $link . PHP_EOL;
  }
}

