<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\MarginPenjualanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StokController;

// Format: Route::get('url', [Controller::class, 'method']);

Route::get('/', [AuthController::class, 'login'])->middleware('Guest');
Route::get('/login', [AuthController::class, 'login'])->middleware('Guest');
Route::post('/login', [AuthController::class, 'prosesLogin']);
Route::get('/register', [AuthController::class, 'register'])->middleware('Guest');
Route::post('/register', [AuthController::class, 'prosesRegister']);

Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/forbidden', [AuthController::class, 'forbidden']);

// ROUTE BUAT BARANG
Route::get('/barang', [BarangController::class, 'index'])->middleware('Authenticate');
Route::post('/barang', [BarangController::class, 'store'])->middleware(['Authenticate', 'Role:superadmin']);
Route::put('/barang/{id}', [BarangController::class, 'update'])->middleware(['Authenticate', 'Role:superadmin']);
Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->middleware(['Authenticate', 'Role:superadmin']);

// ROUTE BUAT SATUAN
Route::get('/satuan', [SatuanController::class, 'index'])->middleware('Authenticate');
Route::post('/satuan', [SatuanController::class, 'store'])->middleware(['Authenticate', 'Role:superadmin']);
Route::put('/satuan/{id}', [SatuanController::class, 'update'])->middleware(['Authenticate', 'Role:superadmin']);
Route::delete('/satuan/{id}', [SatuanController::class, 'destroy'])->middleware(['Authenticate', 'Role:superadmin']);

// ROUTE BUAT VENDOR
Route::get('/vendor', [VendorController::class, 'index'])->middleware('Authenticate');
Route::post('/vendor', [VendorController::class, 'store'])->middleware(['Authenticate', 'Role:superadmin']);
Route::put('/vendor/{id}', [VendorController::class, 'update'])->middleware(['Authenticate', 'Role:superadmin']);
Route::delete('/vendor/{id}', [VendorController::class, 'destroy'])->middleware(['Authenticate', 'Role:superadmin']);

// ROUTE BUAT MARGIN PENJUALAN
Route::get('/marginpenjualan', [MarginPenjualanController::class, 'index'])->middleware('Authenticate');
Route::post('/marginpenjualan', [MarginPenjualanController::class, 'store'])->middleware(['Authenticate', 'Role:superadmin']);
Route::put('/marginpenjualan/{id}', [MarginPenjualanController::class, 'update'])->middleware(['Authenticate', 'Role:superadmin']);
Route::delete('/marginpenjualan/{id}', [MarginPenjualanController::class, 'destroy'])->middleware(['Authenticate', 'Role:superadmin']);

// ROUTE BUAT USER & ROLE
Route::get('/user', [UserController::class, 'index'])->middleware('Authenticate');
Route::post('/user', [UserController::class, 'storeUser'])->middleware(['Authenticate', 'Role:superadmin']);
Route::put('/user/{id}', [UserController::class, 'updateUser'])->middleware(['Authenticate', 'Role:superadmin']);
Route::delete('/user/{id}', [UserController::class, 'destroyUser'])->middleware(['Authenticate', 'Role:superadmin']);

Route::post('/role', [UserController::class, 'storeRole'])->middleware(['Authenticate', 'Role:superadmin']);
Route::put('/role/{id}', [UserController::class, 'updateRole'])->middleware(['Authenticate', 'Role:superadmin']);
Route::delete('/role/{id}', [UserController::class, 'destroyRole'])->middleware(['Authenticate', 'Role:superadmin']);


// ROUTE UNTUK PENGADAAN BARANG 
Route::get('/pengadaan/barang', [PengadaanController::class, 'index'])->middleware('Authenticate');
Route::get('/pengadaan/barang/create', [PengadaanController::class, 'create'])->middleware(['Authenticate', 'Role:superadmin']);
Route::post('/pengadaan/barang', [PengadaanController::class, 'store'])->middleware(['Authenticate', 'Role:superadmin']);
Route::get('/pengadaan/barang/edit/{id}', [PengadaanController::class, 'edit'])->middleware(['Authenticate', 'Role:superadmin']);
Route::put('/pengadaan/barang/{id}', [PengadaanController::class, 'update'])->middleware(['Authenticate', 'Role:superadmin']);
Route::get('/pengadaan/barang/dt/{id}', [PengadaanController::class, 'getdt'])->middleware('Authenticate');
Route::delete('/pengadaan/barang/{id}', [PengadaanController::class, 'destroy'])->middleware(['Authenticate', 'Role:superadmin']);

// ROUTE UNTUK PENERIMAAN BARANG 
Route::get('/penerimaan/barang', [PenerimaanController::class, 'index'])->middleware('Authenticate');
Route::get('/penerimaan/barang/create', [PenerimaanController::class, 'create'])->middleware(['Authenticate', 'Role:superadmin']);
Route::get('/penerimaan/barang/detailpengadaan/{id}', [PenerimaanController::class, 'getDetailPengadaan'])->middleware('Authenticate');
Route::post('/penerimaan/barang', [PenerimaanController::class, 'store'])->middleware(['Authenticate', 'Role:superadmin']);
Route::get('/penerimaan/barang/edit/{id}', [PenerimaanController::class, 'edit'])->middleware(['Authenticate', 'Role:superadmin']);
Route::put('/penerimaan/barang/{id}', [PenerimaanController::class, 'update'])->middleware(['Authenticate', 'Role:superadmin']);
Route::get('/penerimaan/barang/dt/{id}', [PenerimaanController::class, 'getdt'])->middleware('Authenticate');
Route::delete('/penerimaan/barang/{id}', [PenerimaanController::class, 'destroy'])->middleware(['Authenticate', 'Role:superadmin']);
Route::get('/penerimaan/barang/retur/{id}', [PenerimaanController::class, 'retur'])->middleware(['Authenticate', 'Role:superadmin']);
Route::post('/penerimaan/barang/retur', [PenerimaanController::class, 'storeRetur'])->middleware(['Authenticate', 'Role:superadmin']);

// ROUTE UNTUK PENJUALAN BARANG 
Route::get('/penjualan/barang', [PenjualanController::class, 'index'])->middleware('Authenticate');
Route::get('/penjualan/barang/create', [PenjualanController::class, 'create'])->middleware(['Authenticate', 'Role:superadmin']);
Route::get('/penjualan/barang/detailpenjualan/{id}', [PenjualanController::class, 'getDetailPenjualan'])->middleware('Authenticate');
Route::post('/penjualan/barang', [PenjualanController::class, 'store'])->middleware(['Authenticate', 'Role:superadmin']);
Route::get('/penjualan/barang/edit/{id}', [PenjualanController::class, 'edit'])->middleware(['Authenticate', 'Role:superadmin']);
Route::put('/penjualan/barang/{id}', [PenjualanController::class, 'update'])->middleware(['Authenticate', 'Role:superadmin']);
Route::get('/penjualan/barang/dt/{id}', [PenjualanController::class, 'getdt'])->middleware('Authenticate');
Route::delete('/penjualan/barang/{id}', [PenjualanController::class, 'destroy'])->middleware(['Authenticate', 'Role:superadmin']);


// ROUTE BUAT STOK
Route::get('/stok', [StokController::class, 'index'])->middleware('Authenticate');
Route::get('/stok/mutasi/{id}', [StokController::class, 'mutasi'])->middleware('Authenticate');

