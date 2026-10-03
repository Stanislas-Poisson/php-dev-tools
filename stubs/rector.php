<?php

declare(strict_types=1);

use StanislasPoisson\DevTools\Rector;

// The lowest version of PHP of the project, and the directories to process.
return Rector::configure(__DIR__, php: '8.3');
