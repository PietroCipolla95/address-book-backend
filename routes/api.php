<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\AnagraphicController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

// Permissions routes

// permissions index
Route::get('/permissions', [PermissionController::class, 'index']);

// User routes

// index of all users
Route::get('/users', [UserController::class, 'index']);

// show a single user
Route::get('/user/{user:id}', [UserController::class, 'show']);

// create a new user
Route::post('/user', [UserController::class, 'store']);

// update a single user
Route::patch('/user/{user:id}', [UserController::class, 'update']);

// delete a single user
Route::delete('/user/{user:id}', [UserController::class, 'destroy']);

// login user
Route::post('/user/login', [UserController::class, 'loginUser']);


// Anagraphic routes

// index of all anagraphics
Route::get('/anagraphics', [AnagraphicController::class, 'index']);

// show a single anagraphic
Route::get('/anagraphic/{anagraphic:id}', [AnagraphicController::class, 'show']);

// create a new anagraphic
Route::post('/anagraphic', [AnagraphicController::class, 'store']);

// update a single anagraphic
Route::patch('/anagraphic/{anagraphic:id}', [AnagraphicController::class, 'update']);

// delete a single anagraphic
Route::delete('/anagraphic/{anagraphic:id}', [AnagraphicController::class, 'destroy']);

// search anagraphic
Route::get('/anagraphics/search', [AnagraphicController::class, 'search']);

// fetch all the contacts for a single anagraphic
Route::get('/anagraphic/{anagraphic:id}/contacts', [AnagraphicController::class, 'getContacts']);

// add a contact to the single anagraphic
Route::post('/anagraphic/{anagraphic:id}/contact', [AnagraphicController::class, 'addContact']);

// Contact routes

// show a single contact
Route::get('/contacts/{contact:id}', [ContactController::class, 'show']);

// update the contact
Route::patch('/contact/{contact:id}', [ContactController::class, 'update']);

// delete the contact
Route::delete('/contact/{contact:id}', [ContactController::class, 'destroy']);

// search contact
Route::get('/contacts/search/{contact:type}', [ContactController::class, 'searchByType']);
