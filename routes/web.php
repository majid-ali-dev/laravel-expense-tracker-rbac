<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Admin routes
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');
});

// Manager routes
Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/manager', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    // RoleController
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/{id}/update', [RoleController::class, 'update'])->name('roles.update');
    Route::post('/roles/{id}/delete', [RoleController::class, 'destroy'])->name('roles.delete');
    // PermissionController
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::post('/permissions/{id}/update', [PermissionController::class, 'update'])->name('permissions.update');
    Route::post('/permissions/{id}/delete', [PermissionController::class, 'destroy'])->name('permissions.delete');
    // UserController
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/delete', [UserController::class, 'delete'])->name('users.delete');
   // Role & Permission Assignment
    Route::get('/role-permissions', [RolePermissionController::class, 'index'])
        ->name('role.permissions.index');
    Route::get('/role-permissions/{role}', [RolePermissionController::class, 'edit'])
        ->name('role.permissions.edit');
    Route::post('/role-permissions/{role}', [RolePermissionController::class, 'update'])
        ->name('role.permissions.update');
});

// Staff routes
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/staff', [StaffController::class, 'dashboard'])
        ->name('staff.dashboard');
});

// Member routes
Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/member', [MemberController::class, 'dashboard'])
        ->name('member.dashboard');
});

Route::fallback(function () {
    return redirect()->route('login')->with('error', 'Page not found');
});
