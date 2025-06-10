<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('update-tree-data')
    ->dailyAt('04:33')
    ->onSuccess(function () {
        Log::info('Tree data updated.');
    })
    ->onFailure(function () {
        Log::error('Failed to update tree data.');
    });
