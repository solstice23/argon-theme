<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';

$readmeText = (new \voku\PhpReadmeHelper\GenerateApi())->generate(
    __DIR__ . '/../src/',
    __DIR__ . '/docs/api.md',
    [
        \voku\helper\DomParserInterface::class,
        \voku\helper\SimpleHtmlDomNodeInterface::class,
        \voku\helper\SimpleHtmlDomInterface::class
    ]
);

file_put_contents(__DIR__ . '/../README_API.md', $readmeText);
