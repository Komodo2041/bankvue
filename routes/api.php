<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/imports/{id}', "App\Http\Controllers\Api\ImportController@importRecord" ); 
Route::post('/imports', "App\Http\Controllers\Api\ImportController@addFile" );
Route::get('/imports', "App\Http\Controllers\Api\ImportController@agetImports" );
 

