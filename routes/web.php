<?php

use Carnage\BingImageExtractor\BingImageExtractor;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/{query}', function ($query) {
    $start = microtime(true);
    $bingExtractor = new BingImageExtractor();
    $time_elapsed_secs = microtime(true) - $start;
    dd($bingExtractor->getImageLinks($query), count($bingExtractor->getImageLinks($query)), $time_elapsed_secs);
});
