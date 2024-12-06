<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\CashFlowController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DashboardController;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Produtos
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{product}', [ProductController::class, 'show']);
        Route::put('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);
        Route::get('/check-sku/{sku}', [ProductController::class, 'checkSku']);
        Route::get('/categories', [ProductController::class, 'categories']);
        Route::put('/{product}/stock', [ProductController::class, 'updateStock']);
    });

    // Vendas
    Route::prefix('sales')->group(function () {
        Route::get('/', [SaleController::class, 'index']);
        Route::post('/', [SaleController::class, 'store']);
        Route::get('/{sale}', [SaleController::class, 'show']);
        Route::put('/{sale}', [SaleController::class, 'update']);
        Route::delete('/{sale}', [SaleController::class, 'destroy']);
    });

    // Usuários
    Route::prefix('users')->middleware('permission:manage-users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    // Papéis e Permissões
    Route::prefix('roles')->middleware('permission:manage-roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{role}', [RoleController::class, 'show']);
        Route::put('/{role}', [RoleController::class, 'update']);
        Route::delete('/{role}', [RoleController::class, 'destroy']);
    });

    Route::prefix('permissions')->middleware('permission:manage-roles')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
    });

    // Empresas
    Route::prefix('companies')->middleware('permission:manage-companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::get('/{company}', [CompanyController::class, 'show']);
        Route::put('/{company}', [CompanyController::class, 'update']);
        Route::delete('/{company}', [CompanyController::class, 'destroy']);
    });

    // Lojas
    Route::prefix('stores')->middleware('permission:manage-stores')->group(function () {
        Route::get('/', [StoreController::class, 'index']);
        Route::post('/', [StoreController::class, 'store']);
        Route::get('/{store}', [StoreController::class, 'show']);
        Route::put('/{store}', [StoreController::class, 'update']);
        Route::delete('/{store}', [StoreController::class, 'destroy']);
    });

    // Inventário
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index']);
        Route::post('/', [InventoryController::class, 'store']);
        Route::get('/{inventory}', [InventoryController::class, 'show']);
        Route::put('/{inventory}', [InventoryController::class, 'update']);
        Route::delete('/{inventory}', [InventoryController::class, 'destroy']);
        Route::post('/{inventory}/close', [InventoryController::class, 'close']);
        Route::post('/{inventory}/reopen', [InventoryController::class, 'reopen']);
    });

    // Fluxo de Caixa
    Route::prefix('cash-flow')->group(function () {
        Route::get('/', [CashFlowController::class, 'index']);
        Route::post('/', [CashFlowController::class, 'store']);
        Route::get('/{cashFlow}', [CashFlowController::class, 'show']);
        Route::put('/{cashFlow}', [CashFlowController::class, 'update']);
        Route::delete('/{cashFlow}', [CashFlowController::class, 'destroy']);
    });
});
