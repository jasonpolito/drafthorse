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
Route::view('/calc', 'calc');
Route::view('/t9', 't9');

Route::get('/{slug?}', function ($slug) {
    $record = Record::findBySlug($slug);
    // $record = $record->withRelations();
    $data = Record::getData($record);
    // dd($data);
    return view('page', ['record' => $record, 'data' => $data]);
})->name('records.show')->where(['slug' => '.*']);
