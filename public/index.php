<?php
date_default_timezone_set($_SERVER['APP_TIMEZONE'] ?? 'Africa/Douala');

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
