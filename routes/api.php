<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Product Routes
    Route::prefix('products')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->middleware('permission:view-products');
        Route::post('/', [ProductController::class, 'store'])->middleware('permission:create-products');
        Route::get('/{product}', [ProductController::class, 'show'])->middleware('permission:view-products');
        Route::put('/{product}', [ProductController::class, 'update'])->middleware('permission:edit-products');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->middleware('permission:delete-products');
        
        // Stock Management
        Route::post('/{product}/stock', [ProductController::class, 'updateStock'])
            ->middleware('permission:manage-stock');
        Route::get('/{product}/stock-history', [ProductController::class, 'stockHistory'])
            ->middleware('permission:view-stock');
        
        // Categories
        Route::get('/categories', [ProductController::class, 'categories'])
            ->middleware('permission:view-products');
    });

    // Sale Routes
    Route::prefix('sales')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->middleware('permission:view-sales');
        Route::post('/', [SaleController::class, 'store'])->middleware('permission:create-sales');
        Route::get('/{sale}', [SaleController::class, 'show'])->middleware('permission:view-sales');
        Route::post('/{sale}/complete', [SaleController::class, 'complete'])->middleware('permission:create-sales');
        Route::post('/{sale}/cancel', [SaleController::class, 'cancel'])->middleware('permission:cancel-sales');
        Route::get('/report', [SaleController::class, 'report'])->middleware('permission:view-reports');
        Route::get('/report/daily', [SaleController::class, 'dailyReport'])->middleware('permission:view-reports');
    });

    // Cash Flow Routes
    Route::prefix('cash-flow')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [CashFlowController::class, 'index'])->middleware('permission:view-cash-flow');
        Route::post('/', [CashFlowController::class, 'store'])->middleware('permission:create-cash-flow');
        Route::get('/{cashFlow}', [CashFlowController::class, 'show'])->middleware('permission:view-cash-flow');
        Route::get('/balance', [CashFlowController::class, 'balance'])->middleware('permission:view-cash-flow');
        Route::get('/report', [CashFlowController::class, 'report'])->middleware('permission:view-reports');
        Route::post('/{cashFlow}/update', [CashFlowController::class, 'update'])->middleware('permission:edit-cash-flow');
        Route::delete('/{cashFlow}', [CashFlowController::class, 'destroy'])->middleware('permission:delete-cash-flow');
    });

    // Inventory Routes
    Route::prefix('inventory')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->middleware('permission:view-stock');
        Route::post('/', [InventoryController::class, 'store'])->middleware('permission:manage-stock');
        Route::get('/{id}', [InventoryController::class, 'show'])->middleware('permission:view-stock');
        Route::post('/{id}/update-stock', [InventoryController::class, 'updateStock'])->middleware('permission:manage-stock');
        Route::post('/transfer-stock', [InventoryController::class, 'transferStock'])->middleware('permission:manage-stock');
        Route::get('/report/low-stock', [InventoryController::class, 'lowStockReport'])->middleware('permission:view-stock');
    });

    // Company Routes
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::get('/{company}', [CompanyController::class, 'show']);
        Route::put('/{company}', [CompanyController::class, 'update']);
        Route::delete('/{company}', [CompanyController::class, 'destroy']);
    });

    // Store Routes
    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'index']);
        Route::post('/', [StoreController::class, 'store']);
        Route::get('/{id}', [StoreController::class, 'show']);
        Route::put('/{id}', [StoreController::class, 'update']);
        Route::delete('/{id}', [StoreController::class, 'destroy']);
    });

    // User Management Routes
    Route::middleware(['auth:sanctum'])->group(function () {
        // User Routes
        Route::prefix('users')->middleware('permission:manage-users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::get('/{user}', [UserController::class, 'show']);
            Route::put('/{user}', [UserController::class, 'update']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
            Route::post('/{user}/roles', [UserController::class, 'assignRole']);
            Route::delete('/{user}/roles/{role}', [UserController::class, 'removeRole']);
        });

        // Role Routes
        Route::prefix('roles')->middleware('permission:manage-roles')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::get('/{role}', [RoleController::class, 'show']);
            Route::put('/{role}', [RoleController::class, 'update']);
            Route::delete('/{role}', [RoleController::class, 'destroy']);
            Route::get('/{role}/users', [RoleController::class, 'listUsers']);
            Route::get('/{role}/permissions', [RoleController::class, 'listPermissions']);
            Route::post('/{role}/permissions', [RoleController::class, 'assignPermission']);
            Route::delete('/{role}/permissions/{permission}', [RoleController::class, 'removePermission']);
        });

        // Permission Routes
        Route::prefix('permissions')->middleware('permission:manage-permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index']);
            Route::post('/', [PermissionController::class, 'store']);
            Route::get('/{permission}', [PermissionController::class, 'show']);
            Route::put('/{permission}', [PermissionController::class, 'update']);
            Route::delete('/{permission}', [PermissionController::class, 'destroy']);
            Route::get('/{permission}/roles', [PermissionController::class, 'listRoles']);
        });
    });
});
