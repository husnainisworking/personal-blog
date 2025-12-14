<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use App\Mail\BrevoApiTransport;

class BrevoMailServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Mail::extend('brevo-api', function () {
            return new BrevoApiTransport();
        });
    }
}
