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
use Illuminate\Support\Facades\Session;

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

    // Plots
    Route::get('/plots', [PlotController::class, 'index'])->name('plots.index');
    Route::get('/plots/create', [PlotController::class, 'create'])->name('plots.create');
    Route::post('/plots', [PlotController::class, 'store'])->name('plots.store');
    Route::get('/plots/{plot}/edit', [PlotController::class, 'edit'])->name('plots.edit');
    Route::patch('/plots/{plot}', [PlotController::class, 'update'])->name('plots.update');
    Route::delete('/plots/{plot}', [PlotController::class, 'destroy'])->name('plots.destroy');

    // Farmers view - CHANGED NAME TO 'main.farmer.index' TO AVOID CLASH
    Route::get('/farmers', [FarmerController::class, 'index'])->name('main.farmer.index');
    Route::get('/farmers/create', [FarmerController::class, 'create'])->name('main.farmer.create');
    Route::post('/farmers', [FarmerController::class, 'store'])->name('main.farmer.store');
    Route::get('/farmers/{farmer}/edit', [FarmerController::class, 'edit'])->name('main.farmer.edit');
    Route::patch('/farmers/{farmer}', [FarmerController::class, 'update'])->name('main.farmer.update');
    Route::delete('/farmers/{farmer}', [FarmerController::class, 'destroy'])->name('main.farmer.destroy');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin', 'approved'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/pending-users', [AdminController::class, 'pendingUsers'])->name('pending-users');
    Route::patch('/approve-user/{user}', [AdminController::class, 'approve'])->name('users.approve');
    Route::patch('/reject-user/{user}', [AdminController::class, 'reject'])->name('reject-user');

    // User management
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create-user');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::patch('/users/{id}/restore', [AdminController::class, 'restoreUser'])->name('users.restore');

    // Farmers view for Admin
    Route::get('/farmers', [AdminController::class, 'listFarmers'])->name('farmer.index');
    Route::get('/farmers/create', [AdminController::class, 'createFarmer'])->name('farmer.create');
    Route::post('/farmers', [AdminController::class, 'storeFarmer'])->name('farmer.store');
    Route::get('/farmers/{farmer}/edit', [AdminController::class, 'editFarmer'])->name('farmer.edit');
    Route::patch('/farmers/{farmer}', [AdminController::class, 'updateFarmer'])->name('farmer.update');
    Route::delete('/farmers/{farmer}', [AdminController::class, 'destroyFarmer'])->name('farmer.destroy');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/forecasting', [AnalyticsController::class, 'forecasting'])->name('analytics.forecasting');

    // Market prices
    Route::get('/market-prices', [MarketPriceController::class, 'index'])->name('market-prices.index');
    Route::get('/market-prices/create', [MarketPriceController::class, 'create'])->name('market-prices.create');
    Route::post('/market-prices', [MarketPriceController::class, 'store'])->name('market-prices.store');
    Route::get('/market-prices/{marketPrice}/edit', [MarketPriceController::class, 'edit'])->name('market-prices.edit');
    Route::patch('/market-prices/{marketPrice}', [MarketPriceController::class, 'update'])->name('market-prices.update');
    Route::delete('/market-prices/{marketPrice}', [MarketPriceController::class, 'destroy'])->name('market-prices.destroy');

    // Reports
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
});

// Staff-only routes
Route::middleware(['auth', 'role:staff', 'approved'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'staffDashboard'])->name('dashboard');
    // If you add farmer routes here later, they will be 'staff.farmer.index' automatically
});

// Include auth routes
require __DIR__ . '/auth.php';