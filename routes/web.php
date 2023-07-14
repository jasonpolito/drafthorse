<?php

use App\Models\Block;
use App\Models\Page;
use App\Models\Partial;
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
Route::view('/calc', 'calc');
Route::view('/template', 'template');
Route::view('/t9', 't9');

Route::get('/{slug?}', function ($slug) {
    $record = Record::findBySlug($slug);
    if (!$record) {
        abort(404);
    }
    $data = Record::getData($record);
    return view('page', ['record' => $record, 'data' => $data]);
})->name('records.show')->where(['slug' => '.*']);
