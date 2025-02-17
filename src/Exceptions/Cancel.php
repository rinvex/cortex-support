<?php

declare(strict_types=1);

namespace Cortex\Support\Exceptions;

use Exception;

class Cancel extends Exception
{
    protected bool $shouldRollbackDatabaseTransaction = false;

    public function rollBackDatabaseTransaction(bool $condition = true): static
    {
        $this->shouldRollbackDatabaseTransaction = $condition;

        return $this;
    }

    public function shouldRollbackDatabaseTransaction(): bool
    {
        return $this->shouldRollbackDatabaseTransaction;
    }
}
