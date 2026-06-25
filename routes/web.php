<?php

use Illuminate\Support\Facades\Route;

// routes/web.php

// This captures any URL route strings and hands rendering authority directly over to Vue Router
Route::get('/{any}', function () {
    return view('app'); // Ensure this view has your <div id="app"> container loading app.js
})->where('any', '.*');
