<?php

namespace App\Providers;

use App\Mail\BrevoApiTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;

class BrevoMailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Mail::extend('brevo-api', function () {
            return new BrevoApiTransport;
        });
    }
}
