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
    $parts = explode('/', $slug);
    $last = $parts[count($parts) - 1];
    $page = Page::where('slug', $last)->first();
    if ($page->getSlug() == $slug) {
        return view('page', compact('page'));
    } else {
        abort(404);
    }
})->name('pages.show')->where(['slug' => '.*']);
