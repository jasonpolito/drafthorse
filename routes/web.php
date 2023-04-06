<?php

use App\Models\Page;
use App\Models\Record;
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
    $parts = explode('/', $slug);
    $last = $parts[count($parts) - 1];
    $page = Record::where('slug', $last)->with(['children', 'parent'])->first();
    $page->buildTaxonomy();
    return collect($page);
    if ($page->getSlug() == $slug) {
        return view('page', compact('page'));
    } else {
        abort(404);
    }
})->name('pages.show')->where(['slug' => '.*']);
