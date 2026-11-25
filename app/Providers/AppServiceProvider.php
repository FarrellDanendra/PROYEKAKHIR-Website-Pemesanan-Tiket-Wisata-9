<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    Model::preventLazyLoading(! app()->isProduction());
    Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
    View::composer('layouts.app', function ($view) {
        $pendingCount = 0;
        if (auth()->check() && auth()->user()->hasRole('wisatawan') && ! request()->routeIs('pemesanan.*')) {
            Pemesanan::syncExpiredStatuses();
            $pendingCount = Pemesanan::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->where(function ($q) {
                    $q->whereNull('batas_bayar')->orWhere('batas_bayar', '>', now());
                })
                ->count();
        }
        $view->with('pendingPaymentCount', $pendingCount);
    });
}
}