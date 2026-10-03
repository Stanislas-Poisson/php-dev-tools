<?php

declare(strict_types=1);

use StanislasPoisson\DevTools\Insights;

require __DIR__ . '/vendor/autoload.php';

return Insights::config([
    'exclude' => ['config', 'stubs'],
]);
