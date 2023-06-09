<?php

use App\Http\Resources\RecordsCollection;
use App\Models\Page;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::prefix('/api')->group(function () {
Route::get('/records', function () {
    return new RecordsCollection(Record::all());
});
Route::get('/records/{id}', function ($id) {
    return new RecordsCollection(Record::where('id', $id));
});
// });
