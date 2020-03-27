<?php
declare(strict_types=1);

use LeanpubBookClub\Infrastructure\ProductionServiceContainer;

require __DIR__ . '/vendor/autoload.php';

$serviceContainer = ProductionServiceContainer::createFromEnvironmentVariables();

echo "Importing all purchases.\n";
$serviceContainer->application()->importAllPurchases();

// Run it once more to show that the second time it'll be much faster
echo "Importing all purchases again.\n";
$serviceContainer->application()->importAllPurchases();
