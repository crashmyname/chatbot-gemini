<?php
use App\Controllers\AiController;
use Support\Route;
use Support\View;
use Support\AuthMiddleware; //<-- Penambahan Middleware atau session login

// handleMiddleware();
// Route::get('/',function(){
//     View::render('welcome/welcome');
// });
Route::get('/',[AiController::class, 'chat']);
Route::post('/gemini',[AiController::class, 'chat']);