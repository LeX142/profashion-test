<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    abort(404);
});

Route::view('/docs', 'rapidoc.index')->name('api-rapidoc');
