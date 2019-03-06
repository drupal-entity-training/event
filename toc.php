<?php

declare(strict_types=1);

$file = file('index.md');
$headers = array_filter($file, function (string $line) {
  return substr($line, 0, 3) === '###';
});

foreach ($headers as $header) {
  list(, $numeral, $title) = explode(' ', rtrim($header), 3);
  list($h3, $h4) = explode('.', $numeral . '.0');

  $link = '[' . $title . '](#' . strtolower(str_replace(' ', '-', $title)) . ')';
  if ($header[3] !== '#') {
    print $h3 . '. ' . $link . PHP_EOL;
  }
  else {
    print '      ' . $h4 . '. ' . $link . PHP_EOL;
  }
}

