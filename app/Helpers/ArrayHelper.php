<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class ArrayHelper
{
    public static function camelToSnakeKeys(array $array): array
    {
        return collect($array)
            ->keyBy(fn ($value, $key) => Str::snake($key))
            ->all();
    }

    public static function DecodeHashIds(array $fields, array $data): array
    {
        foreach ($fields as $field) {
            if (! isset($data[$field])) {
                continue;
            }

            if (is_array($data[$field])) {
                $data[$field] = array_map(function ($hashId) {
                    return Hashids::decode($hashId)[0] ?? null;
                }, $data[$field]);
            } else {
                $data[$field] = Hashids::decode($data[$field])[0] ?? null;
            }
        }

        return $data;
    }
}
