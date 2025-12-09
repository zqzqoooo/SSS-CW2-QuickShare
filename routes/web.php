<?php

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


// ---------- Test page ----------
use App\Http\Controllers\TestController;

/*
 * 超级管理员调试台  - 仅限管理员访问
  * Super Admin Debugging Console - Admins Only
 */
Route::middleware(['auth', 'verified', 'admin'])->group(function () {

// 1. 调试台主页 - Debug Console Home
Route::get('/test-debug', [TestController::class, 'index'])->name('debug.index');

// 2. 取件码测试 - Code Management Tests
Route::post('/test-debug/code/get', [TestController::class, 'getCode'])->name('debug.code.get');
Route::post('/test-debug/code/recycle', [TestController::class, 'recycleCode'])->name('debug.code.recycle');

// 3. 文件服务测试 - File Service Tests
Route::post('/test-debug/file/upload', [TestController::class, 'uploadTest'])->name('debug.file.upload');
Route::get('/test-debug/file/download', [TestController::class, 'downloadTest'])->name('debug.file.download');

// 4. 管理员监控 & 清理
Route::get('/test-debug/admin/list', [TestController::class, 'listOccupiedCodes'])->name('debug.admin.list');
Route::post('/test-debug/admin/delete', [TestController::class, 'manualDelete'])->name('debug.admin.delete');
Route::post('/test-debug/admin/cleanup', [TestController::class, 'triggerCleanup'])->name('debug.admin.cleanup');

// 5. 用户管理调试
Route::get('/test-debug/users', [TestController::class, 'listUsers'])->name('debug.users.list');
Route::post('/test-debug/users/ban', [TestController::class, 'debugToggleBan'])->name('debug.users.ban');

// 6. 邮件服务测试
Route::get('/test-debug/email', [TestController::class, 'emailTestForm'])->name('debug.email.form');
Route::post('/test-debug/email/send', [TestController::class, 'sendTestEmail'])->name('debug.email.send');
});
// ---------- Test page ----------

use App\Http\Controllers\AdminController;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // 页面路由
    Route::get('/', [AdminController::class, 'index'])->name('index'); // 仪表盘
    Route::get('/users', [AdminController::class, 'users'])->name('users'); // 用户管理
    Route::get('/files', [AdminController::class, 'files'])->name('files'); // 文件管理

    // 动作路由
    Route::post('/users/ban', [AdminController::class, 'toggleBan'])->name('users.ban');
    Route::delete('/files/delete', [AdminController::class, 'deleteFile'])->name('files.delete');
});

// FileController -------------------------
use App\Http\Controllers\FileController;

Route::post('/upload', [FileController::class, 'store'])->name('file.upload');
Route::get('/download', [FileController::class, 'download'])->name('file.download');
Route::get('/success/{code}', [FileController::class, 'success'])->name('file.success');
// FileController -------------------------

// UserFileController ---------------------
use App\Http\Controllers\UserFileController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // index 方法里写了 $files = ... 并传给了视图
    Route::get('/dashboard', [UserFileController::class, 'index'])->name('dashboard');

    // 新增：文件详情页路由
    Route::get('/dashboard/file/{id}', [UserFileController::class, 'show'])->name('user.files.show');
    Route::get('/dashboard/file/{id}/preview', [UserFileController::class, 'preview'])->name('user.files.preview');
    Route::put('/dashboard/file/{id}', [UserFileController::class, 'update'])->name('user.files.update');
    Route::delete('/dashboard/file/{id}', [UserFileController::class, 'destroy'])->name('user.files.destroy');
});
// UserFileController ---------------------

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Route::get('/phpinfo', function () {
//     phpinfo();
// });

require __DIR__.'/auth.php';
