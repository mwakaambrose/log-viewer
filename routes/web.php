<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Facades\LogViewer;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ScanFilesController;
use App\Http\Controllers\DownloadFileController;
use App\Http\Controllers\DownloadFolderController;
use App\Http\Controllers\IsScanRequiredController;
use App\Http\Controllers\SearchProgressController;

Route::domain(LogViewer::getRouteDomain())
    ->middleware(LogViewer::getRouteMiddleware())
    ->prefix(LogViewer::getRoutePrefix())
    ->group(function () {
        Route::get('/', IndexController::class)->name('blv.index');

        Route::get('file/{fileIdentifier}/download', DownloadFileController::class)->name('blv.download-file');
        Route::get('folder/{folderIdentifier}/download', DownloadFolderController::class)->name('blv.download-folder');

        Route::get('is-scan-required', IsScanRequiredController::class)->name('blv.is-scan-required');
        Route::get('scan-files', ScanFilesController::class)->name('blv.scan-files');

        Route::get('search-progress', SearchProgressController::class)->name('blv.search-more');
    });
