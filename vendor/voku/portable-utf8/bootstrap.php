<?php

use voku\helper\Bootup;
use voku\helper\UTF8;

Bootup::initAll(); // Enables UTF-8 for PHP
UTF8::checkForSupport(); // Check UTF-8 support for PHP

if (defined('PORTABLE_UTF8__ENABLE_AUTO_FILTER') === true) {
    Bootup::filterRequestUri(); // Redirects to an UTF-8 encoded URL if it's not already the case
    Bootup::filterRequestInputs(); // Normalizes HTTP inputs to UTF-8 NFC
}
