<?php

declare(strict_types=1);

namespace Cortex\Support\Authorization;

use Closure;
use Illuminate\Auth\Access\Response;

class Denial extends Response
{
    protected ?string $key = null;

    protected ?Closure $getMessageUsing = null;

    public static function make(string $key, string | Closure $message): Response
    {
        $static = static::deny(is_string($message) ? $message : null);

        if ($static instanceof Denial) {
            $static->key($key);

            if ($message instanceof Closure) {
                $static->getMessageUsing($message);
            }
        }

        return $static;
    }

    public function key(?string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getMessageUsing(?Closure $callback): static
    {
        $this->getMessageUsing = $callback;

        return $this;
    }

    public function message(int $count = 1, int $total = 1): ?string
    {
        if ($this->getMessageUsing) {
            return app()->call($this->getMessageUsing, [
                'count' => $count,
                'isAll' => $count === $total,
                'total' => $total,
            ])
                ?? parent::message()
                ?? (($count === 1) && ($total === 1)
                    ? null
                    : $this->message());
        }

        return parent::message();
    }
}
