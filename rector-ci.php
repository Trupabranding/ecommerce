<?php

declare(strict_types=1);

return (include __DIR__.'/rector.php')->withCache(
    cacheDirectory: '/tmp/rector',
    cacheClass: Rector\Caching\ValueObject\Storage\FileCacheStorage::class,
);
