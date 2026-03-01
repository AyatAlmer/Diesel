<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


    Route::post('login', [UserController::class, 'login']);
    Route::prefix('users')->middleware('auth:sanctum')->group(function () {

    Route::middleware('role:admin')->group(function () {
        Route::post('/addUser', [UserController::class, 'store']);
        Route::put('/updateUser/{id}', [UserController::class, 'update']);
        Route::delete('/deleteUser/{id}', [UserController::class, 'destroy']);
        Route::get('/showUserById/{id}', [UserController::class, 'showUserById']);
        // Route::get('/showByEmail/{email}', [UserController::class, 'getByEmail']);
        Route::get('/gitAllUser', [UserController::class, 'getAll']);
        Route::get('/searchUser', [UserController::class, 'searchUser']);

    });

        Route::post('logout', [UserController::class, 'logout']);

    });


    Route::prefix('products')->middleware('auth:sanctum')->group(function () {

        Route::get('/showProductById/{id}', [ProductController::class, 'showProductById']);
        Route::get('/showAllProduct', [ProductController::class, 'showAllProduct']);
        Route::get('/searchProduct', [ProductController::class, 'searchProduct']);
        Route::get('/showDeletedProducts', [ProductController::class, 'showDeletedProducts']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/addProduct', [ProductController::class, 'addProduct']);
        Route::post('/updateProduct/{id}', [ProductController::class, 'updateProduct']);
        Route::delete('/destroyProduct/{id}', [ProductController::class, 'destroyProduct']);
        Route::post('/restoreProduct/{id}', [ProductController::class, 'restoreProduct']);
    });


});



    Route::prefix('category')->middleware('auth:sanctum')->group(function () {

    Route::get('/showAllCategory', [CategoryController::class, 'showAllCategory']);
    Route::get('/showCategoryById/{id}', [CategoryController::class, 'showCategoryById']);

    Route::middleware('role:admin')->group(function () {
        Route::post('/addCategory', [CategoryController::class, 'addCategory']);
        Route::put('/updateCategory/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('/destroyCategory/{id}', [CategoryController::class, 'destroyCategory']);
    });
});

    Route::prefix('order')->middleware('auth:sanctum')->group(function () {

        Route::post('/addOrder', [OrderController::class, 'addOrder']);
        Route::get('/getPurchaseInvoices', [OrderController::class, 'getPurchaseInvoices']);

});


    Route::prefix('sales')->middleware('auth:sanctum')->group(function () {

        Route::post('/store', [SaleController::class, 'store']);
        Route::get('/getSalesInvoices', [SaleController::class, 'getSalesInvoices']);

});



    Route::prefix('transactions')->middleware('auth:sanctum')->group(function () {

        Route::get('/summary', [DashboardController::class, 'summary']);

});

    Route::prefix('location')->middleware('auth:sanctum')->group(function () {
        Route::post('/addLocation', [LocationController::class, 'addLocation']);
        Route::get('/getAll', [LocationController::class, 'getAll']);
        Route::get('/showById/{id}', [LocationController::class, 'showById']);
        Route::put('/updateLocation/{id}', [LocationController::class, 'updateLocation']);
        Route::delete('/destroyLocation/{id}', [LocationController::class, 'destroyLocation']);

        });
