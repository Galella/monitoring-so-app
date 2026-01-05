<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DataScopeService
{
    /**
     * Apply location scoping to a query builder.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param string $table Table name to qualify columns
     * @param object|null $user User object (optional, defaults to auth user)
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    public static function apply($query, $table, $user = null)
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return $query;
        }

        if ($user->role->name !== 'Super Admin') {
            if ($user->wilayah_id) {
                $query->where("{$table}.wilayah_id", $user->wilayah_id);
            }
            if ($user->area_id) {
                if ($table === 'coins') {
                    $areaName = $user->area->name ?? null;
                    $query->where(function($q) use ($table, $user, $areaName) {
                        $q->where("{$table}.area_id", $user->area_id);
                        if ($areaName) {
                            $q->orWhere("{$table}.stasiun_asal", $areaName);
                        }
                    });
                } else {
                    $query->where("{$table}.area_id", $user->area_id);
                }
            }
        }

        return $query;
    }

    /**
     * Get the closure for matching Coins and CMs.
     *
     * @return \Closure
     */
    public static function matchCondition()
    {
        return function($query) {
             $query->select(DB::raw(1))
                   ->from('coins')
                   ->whereColumn('coins.container', 'cms.container')
                   ->whereColumn('coins.cm', 'cms.cm');
        };
    }

    /**
     * Get the closure for reverse matching (CMs exists in Coins).
     *
     * @return \Closure
     */
    public static function reverseMatchCondition()
    {
        return function($query) {
             $query->select(DB::raw(1))
                   ->from('cms')
                   ->whereColumn('coins.container', 'cms.container')
                   ->whereColumn('coins.cm', 'cms.cm');
        };
    }
}
