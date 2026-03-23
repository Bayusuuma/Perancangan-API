<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\MapelController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\JadwalController;

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('guru', GuruController::class);
    Route::apiResource('mapel', MapelController::class);
    Route::apiResource('kelas', KelasController::class);
    Route::apiResource('siswa', SiswaController::class);
    Route::apiResource('jadwal', JadwalController::class);
});