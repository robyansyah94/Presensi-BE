<?php

namespace App\Providers;

use App\Events\PresensiRecorded;
use App\Listeners\ProcessIntegrityPoints;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Daftar event → listener mapping.
     * Karena extend EventServiceProvider (bukan ServiceProvider biasa),
     * property $listen ini akan dibaca dan didaftarkan otomatis oleh Laravel.
     */
    protected $listen = [
        PresensiRecorded::class => [
            ProcessIntegrityPoints::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
