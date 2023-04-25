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
    $record = Record::findBySlug($slug);
    $record->withRelations();
    $data = $record->getData();
    // return $record;
    return view('page', ['page' => $record, 'data' => $data]);
})->name('pages.show')->where(['slug' => '.*']);
