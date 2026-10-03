<?php

declare(strict_types=1);

use StanislasPoisson\DevTools\Rector;

require __DIR__ . '/vendor/autoload.php';

return Rector::configure(__DIR__, php: '8.3');
