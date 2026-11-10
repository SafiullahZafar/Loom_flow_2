<?php

$loomCount = max(1, (int) env('LOOM_COUNT', 24));

return [
    'looms' => array_map(
        static fn (int $loomNumber): string => sprintf('Loom-%02d', $loomNumber),
        range(1, $loomCount),
    ),
];
