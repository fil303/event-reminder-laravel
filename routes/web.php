<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get ("/", [ EventController::class, "index" ])->name("event.index");
Route::post("/", [ EventController::class, "store" ])->name("event.store");
Route::get ("/event/delete/{id?}", [ EventController::class, "delete" ])->name("event.delete");


// Route::group([ "prefix" => "event", "as" => "event" ], function(){
//     Route::get("/", [ EventController::class, "index" ])->name("index");
// });