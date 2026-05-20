<?php

declare(strict_types=1);

use App\Actions\Dashboard\GetDashboardSummaryAction;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Livewire\Transactions\CreateTransaction;
use App\Livewire\Voucher\ListVouchers;
use App\Livewire\Voucher\CreateVoucher;
use App\Livewire\Voucher\EditVoucher;
use App\Models\Voucher;
use Illuminate\Support\Facades\Route;

Route::get('/', function (GetDashboardSummaryAction $action) {
    $data = $action->execute();

    $activeVouchers = Voucher::query()
        ->where('is_active', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->count();

    return view('welcome', array_merge($data, [
        'activeVouchers' => $activeVouchers,
    ]));
})->name('home');

/**
 * Dashboard routes
 */

Route::middleware([
    'auth',
    'role:marketing|cashier',
])->group(function () {

    Route::get(
        '/dashboard',
        DashboardController::class,
    )->name('dashboard');

});

/**
 * Cashier routes
 */

Route::middleware([
    'auth',
    'role:cashier',
])->prefix('transactions')
    ->name('transactions.')
    ->group(function () {

        /*
        Create page
        */

        Route::get(
            '/create',
            CreateTransaction::class,
        )->name('create');

        Route::get(
            '/{transaction}/edit',
            CreateTransaction::class,
        )->name('edit');

        /*
        Store
        */

        Route::post(
            '/',
            [TransactionController::class, 'store'],
        )->name('store');

        /*
        Invoice
        */

        Route::get(
            '/{transaction}/invoice',
            [
                TransactionController::class,
                'downloadInvoice',
            ]
        )->name('invoice');

        /*
        Mark as Paid
        */

        Route::post(
            '/{transaction}/pay',
            [TransactionController::class, 'pay']
        )->name('pay');

        Route::post(
            '/{transaction}/delete',
            [TransactionController::class, 'destroy']
        )->name('destroy');

    });

/**
 * Marketing routes - Voucher Management
 */

Route::middleware([
    'auth',
    'role:marketing',
])->prefix('vouchers')
    ->name('vouchers.')
    ->group(function () {

        /*
        List page
        */

        Route::get(
            '/',
            ListVouchers::class,
        )->name('index');

        /*
        Create page
        */

        Route::get(
            '/create',
            CreateVoucher::class,
        )->name('create');

        /*
        Edit page
        */

        Route::get(
            '/{voucher}/edit',
            EditVoucher::class,
        )->name('edit');

    });

require __DIR__.'/settings.php';