<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //  we are on an older version of MySQL
        //  https://laravel.com/docs/master/migrations#creating-indexes
        Schema::defaultStringLength(191);

        Collection::macro('authors', function () {
            return $this->map(function ($row) {
                return collect([$row->user])->merge($row->children->authors());
            })
            ->flatten()
            ->filter(function ($row) {
                return $row;
            })
            ->unique();
        });

        Collection::macro('descendants', function () {
            return $this->map(function ($row) {
                return $row->children->merge($row->children->descendants());
            })
            ->flatten()
            ->filter(function ($row) {
                return $row;
            })
            ->unique();
        });

        Collection::macro('depth', function () {
            $rows = $this;

            return $this->map(function ($row) use ($rows) {
                $row->depth = getDepth($row->id, $rows);

                return $row;
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

function getDepth($id, $rows, $depth = 0) {
    $row = $rows->where('id', $id)->first();

    //  root
    if (!$row) {
        return $depth;
    }

    return getDepth($row->parent_id, $rows, ++$depth);
}