<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // USER MANAGEMENT
    Route::get('/user', [UserController::class, 'list']);
    Route::post('/user', [UserController::class, 'store']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::post('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // CLASSROOM
    Route::get('/classroom', [ClassroomController::class, 'index']);
    Route::get('/classroom/list', [ClassroomController::class, 'list']);
    Route::post('/classroom', [ClassroomController::class, 'store']);
    Route::get('/classroom/{id}', [ClassroomController::class, 'show']);
    Route::post('/classroom/{id}', [ClassroomController::class, 'update']);
    Route::delete('/classroom/{id}', [ClassroomController::class, 'destroy']);

    // STUDENT
    Route::get('/student', [StudentController::class, 'index']);
    Route::get('/student/list', [StudentController::class, 'list']);
    Route::post('/student', [StudentController::class, 'store']);
    Route::get('/student/{id}', [StudentController::class, 'show']);
    Route::post('/student/{id}', [StudentController::class, 'update']);
    Route::delete('/student/{id}', [StudentController::class, 'destroy']);

    // SUBJECT
    Route::get('/subject', [SubjectController::class, 'index']);
    Route::get('/subject/list', [SubjectController::class, 'list']);
    Route::post('/subject', [SubjectController::class, 'store']);
    Route::get('/subject/{id}', [SubjectController::class, 'show']);
    Route::post('/subject/{id}', [SubjectController::class, 'update']);
    Route::delete('/subject/{id}', [SubjectController::class, 'destroy']);

    // ATTENDANCE
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::get('/attendance/list', [AttendanceController::class, 'list']);
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::get('/attendance/{id}', [AttendanceController::class, 'show']);
    Route::post('/attendance/{id}', [AttendanceController::class, 'update']);
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| TEACHER + ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin,teacher'])->group(function () {

    // CLASSROOM
    Route::get('/classroom', [ClassroomController::class, 'index']);
    Route::get('/classroom/list', [ClassroomController::class, 'list']);
    Route::post('/classroom', [ClassroomController::class, 'store']);
    Route::get('/classroom/{id}', [ClassroomController::class, 'show']);
    Route::post('/classroom/{id}', [ClassroomController::class, 'update']);
    Route::delete('/classroom/{id}', [ClassroomController::class, 'destroy']);

    // STUDENT
    Route::get('/student', [StudentController::class, 'index']);
    Route::get('/student/list', [StudentController::class, 'list']);
    Route::post('/student', [StudentController::class, 'store']);
    Route::get('/student/{id}', [StudentController::class, 'show']);
    Route::post('/student/{id}', [StudentController::class, 'update']);
    Route::delete('/student/{id}', [StudentController::class, 'destroy']);

    // SUBJECT
    Route::get('/subject', [SubjectController::class, 'index']);
    Route::get('/subject/list', [SubjectController::class, 'list']);
    Route::post('/subject', [SubjectController::class, 'store']);
    Route::get('/subject/{id}', [SubjectController::class, 'show']);
    Route::post('/subject/{id}', [SubjectController::class, 'update']);
    Route::delete('/subject/{id}', [SubjectController::class, 'destroy']);

    // ATTENDANCE
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::get('/attendance/list', [AttendanceController::class, 'list']);
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::get('/attendance/{id}', [AttendanceController::class, 'show']);
    Route::post('/attendance/{id}', [AttendanceController::class, 'update']);
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy']);
    Route::get('/attendance/count-student', [AttendanceController::class, 'countPerStudent']);
    
});