<?php

namespace App\Http\Filters;

use App\Models\City;
use App\Models\RealEstate\Category;
use App\Models\RealEstate\Equipment;
use App\Models\RealEstate\Layout;
use App\Models\RealEstate\PropertyType;
use App\Models\RealEstate\Regulation;
use App\Models\RealEstate\SubCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PropertyFilter
{
    protected array $allowedFilters = [
        'types' => ['property_type_id', 'whereIn', PropertyType::class],
        'city' => ['city_id', 'where', City::class],
        'category' => ['category_id', 'where', Category::class],
        'subCategory' => ['sub_category_id', 'where', SubCategory::class],
        'regulations' => ['regulations', 'whereHasIn', Regulation::class],
        'layouts' => ['layouts', 'whereHasIn', Layout::class],
        'equipments' => ['equipments', 'whereHasIn', Equipment::class],
        'price' => ['price', 'whereBetween', null],
        'rooms' => ['room', 'where', null],
        'bathrooms' => ['bathroom', 'where', null],
    ];

    public function apply(Builder $builder, Request $request): Builder
    {
        foreach ($this->allowedFilters as $param => [$column, $operator, $model]) {
            $value = $request->input($param);

            if ($this->isEmptyValue($value)) {
                continue;
            }

            // Decode hashids if model is provided
            if ($model) {
                $value = $this->resolveHashids($value, $model);

                if ($this->isEmptyValue($value)) {
                    continue;
                }
            }

            $this->applyFilter($builder, $column, $operator, $value);
        }

        return $builder;
    }

    protected function resolveHashids($value, string $model): array|int|null
    {
        try {
            if (is_array($value)) {
                $decoded = [];
                foreach ($value as $hashid) {
                    $id = $model::findByHashid($hashid)?->getKey();
                    if ($id) {
                        $decoded[] = $id;
                    }
                }
                return $decoded;
            }

            return $model::findByHashid($value)?->getKey();
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function isEmptyValue($value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (is_array($value) && empty($value)) {
            return true;
        }

        return false;
    }

    protected function applyFilter(Builder $builder, string $column, string $operator, $value): void
    {
        switch ($operator) {
            case 'where':
                $builder->where($column, $value);
                break;

            case 'whereIn':
                if (!is_array($value)) {
                    $value = [$value];
                }
                $builder->whereIn($column, $value);
                break;

            case 'whereHasIn':
                if (!is_array($value)) {
                    $value = [$value];
                }
                $builder->whereHas($column, function ($query) use ($value) {
                    $query->whereIn('id', $value);
                });
                break;

            case 'whereGreaterThanOrEqual':
                $builder->where($column, '>=', $value);
                break;

            case 'whereLessThanOrEqual':
                $builder->where($column, '<=', $value);
                break;

            case 'whereBetween':
                if (is_array($value) && count($value) === 2) {
                    $builder->whereBetween($column, $value);
                }
                break;
        }
    }
}
