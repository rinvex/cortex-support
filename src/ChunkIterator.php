<?php

declare(strict_types=1);

namespace Cortex\Support;

use Iterator;
use Generator;

class ChunkIterator
{
    public function __construct(
        protected Iterator $iterator,
        protected int $chunkSize,
    ) {}

    public function get(): Generator
    {
        $this->iterator->rewind();

        $chunk = [];

        for ($i = 0; $this->iterator->valid(); $i++) {
            $chunk[] = $this->iterator->current();

            $this->iterator->next();

            if (count($chunk) !== $this->chunkSize) {
                continue;
            }

            yield $chunk;

            $chunk = [];
        }

        if (count($chunk)) {
            yield $chunk;
        }
    }
}
