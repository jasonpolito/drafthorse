<?php

use App\Models\Page;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{slug?}', function ($slug) {
    // dd($slug);
    $page = Page::where('slug', $slug)->first();
    abort_unless($page, 404);
    return view('page', compact('page'));
})->name('pages.show')->where(['slug' => '.*']);
