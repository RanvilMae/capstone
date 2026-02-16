<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\LatexTransactionController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MarketPriceController;
use App\Http\Controllers\PlotController;
use Illuminate\Support\Facades\Route;

// Redirect '/' to login page
Route::get('/', fn() => redirect()->route('login'));
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'th'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');


// Protected routes for approved users
Route::middleware(['auth', 'approved'])->group(function () {
    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Transactions
    Route::get('/transactions', [LatexTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [LatexTransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [LatexTransactionController::class, 'store'])->name('transactions.store');

    // Plots (accessible by all approved users)
    Route::get('/plots', [PlotController::class, 'index'])->name('plots.index');
    Route::get('/plots/create', [PlotController::class, 'create'])->name('plots.create');
    Route::post('/plots', [PlotController::class, 'store'])->name('plots.store');
    Route::get('/plots/{plot}/edit', [PlotController::class, 'edit'])->name('plots.edit');
    Route::patch('/plots/{plot}', [PlotController::class, 'update'])->name('plots.update');
    Route::delete('/plots/{plot}', [PlotController::class, 'destroy'])->name('plots.destroy');

     // Farmers view 
    Route::get('/farmers', [FarmerController::class, 'index'])->name('farmer.index');
    Route::get('/farmers/create', [FarmerController::class, 'create'])->name('farmer.create');
    Route::post('/farmers', [FarmerController::class, 'store'])->name('farmer.store');
    Route::get('/farmers/{farmer}/edit', [FarmerController::class, 'edit'])->name('farmer.edit');
    Route::patch('/farmers/{farmer}', [FarmerController::class, 'update'])->name('farmer.update');
    Route::delete('/farmers/{farmer}', [FarmerController::class, 'destroy'])->name('farmer.destroy');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin', 'approved'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/pending-users', [AdminController::class, 'pendingUsers'])->name('admin.pending-users');
    Route::patch('/admin/approve-user/{user}', [AdminController::class, 'approve'])->name('admin.approve-user');
    Route::patch('/admin/reject-user/{user}', [AdminController::class, 'reject'])->name('admin.reject-user');

    // User management
    Route::get('/admin/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create-user');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::patch('/admin/users/{id}/restore', [AdminController::class, 'restoreUser'])->name('admin.users.restore');

    // Farmers view for Admin
    Route::get('/admin/farmers', [AdminController::class, 'listFarmers'])->name('admin.farmer.index');
    Route::get('/admin/farmers/create', [AdminController::class, 'createFarmer'])->name('admin.farmer.create');
    Route::post('/admin/farmers', [AdminController::class, 'storeFarmer'])->name('admin.farmer.store');
    Route::get('/admin/farmers/{farmer}/edit', [AdminController::class, 'editFarmer'])->name('admin.farmer.edit');
    Route::patch('/admin/farmers/{farmer}', [AdminController::class, 'updateFarmer'])->name('admin.farmer.update');
    Route::delete('/admin/farmers/{farmer}', [AdminController::class, 'destroyFarmer'])->name('admin.farmer.destroy');

    // Analytics
    Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/admin/analytics/forecasting', [AnalyticsController::class, 'forecasting'])->name('admin.analytics.forecasting');

    // Market prices
    Route::get('/admin/market-prices', [MarketPriceController::class, 'index'])->name('admin.market-prices.index');
    Route::get('/admin/market-prices/create', [MarketPriceController::class, 'create'])->name('admin.market-prices.create');
    Route::post('/admin/market-prices', [MarketPriceController::class, 'store'])->name('admin.market-prices.store');
    Route::get('/admin/market-prices/{marketPrice}/edit', [MarketPriceController::class, 'edit'])->name('admin.market-prices.edit');
    Route::patch('/admin/market-prices/{marketPrice}', [MarketPriceController::class, 'update'])->name('admin.market-prices.update');
    Route::delete('/admin/market-prices/{marketPrice}', [MarketPriceController::class, 'destroy'])->name('admin.market-prices.destroy');

    // Reports
    Route::get('/admin/reports', [DashboardController::class, 'reports'])->name('admin.reports');
});

// Staff-only routes
Route::middleware(['auth', 'role:staff', 'approved'])->group(function () {
    Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('dashboard.staff');

});

// Include auth routes (login, register, forgot password)
require __DIR__ . '/auth.php';
 