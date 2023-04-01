<?php

use App\Http\Resources\PagesCollection;
use App\Models\Page;
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
Route::get('/pages', function () {
    return new PagesCollection(Page::all());
});
Route::get('/pages/{id}', function ($id) {
    return new PagesCollection(Page::where('id', $id));
});
// });
