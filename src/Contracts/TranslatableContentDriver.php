<?php

declare(strict_types=1);

namespace Cortex\Support\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

interface TranslatableContentDriver
{
    public function __construct(string $activeLocale);

    /**
     * @param  class-string<Model>  $model
     */
    public function isAttributeTranslatable(string $model, string $attribute): bool;

    /**
     * @return array<string, mixed>
     */
    public function getRecordAttributesToArray(Model $record): array;

    /**
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $data
     */
    public function makeRecord(string $model, array $data): Model;

    public function setRecordLocale(Model $record): Model;

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateRecord(Model $record, array $data): Model;

    public function applySearchConstraintToQuery(Builder $query, string $column, string $search, string $whereClause, ?bool $isSearchForcedCaseInsensitive = null): Builder;
}
