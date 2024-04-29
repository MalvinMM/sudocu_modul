<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DBController;
use App\Http\Controllers\ERPController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DBFuncController;
use App\Http\Controllers\DBStoreProcController;
use App\Http\Controllers\DBViewController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ReportController;

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
//DISPLAYING REPORT WITH TOKEN FROM ANOTHER APP
Route::get('/report/{erp}/{token}', [ReportController::class, 'apiData']);

Route::controller(AuthController::class)->group(function () {

    Route::get('/', 'index')->name('landing');
    Route::get('/login', 'index')->name('showLogin');
    Route::post('/login', 'login')->name('login')->middleware('web');

    Route::middleware('auth')->group(function () {

        Route::get('/logout', 'logout')->name('logout')->middleware('web');

        Route::prefix('/dashboard')->group(function () {

            Route::get('/', 'dashboard')->name('dashboard');
            Route::get('/{id}/pswChange', 'pswForm')->name('showPswChange');
            Route::post('/{id}/pswChange', 'pswChange')->name('pswChange');

            Route::middleware('isAdmin')->group(function () {

                Route::get('/userList/search', 'search')->name('user.search');
                Route::match(['get', 'post'], '/userList/filtered', 'filter')->name('filter.users');

                Route::get('/userList', 'userList')->name('userList');
                Route::get('/addUser', 'register')->name('registerUser');
                Route::post('/addUser', 'store')->name('storeUser');
                Route::get('/editUser/{id}', 'editUser')->name('editUser');
                Route::put('/editUser/{id}', 'updateUser')->name('updateUser');
                Route::put('/deactivate/{id}', 'deactivateUser')->name('deactivateUser');
                Route::put('/activate/{id}', 'activateUser')->name('activateUser');
            });
        });
    });
});

Route::controller(ERPController::class)->group(function () {
    // Route::prefix('/dashboard')->group(function () {
    Route::get('/{erp}', 'erpMenu')->name('erpMenu')->middleware(['auth']);
});

Route::controller(DBController::class)->group(function () {

    Route::middleware(['auth', 'isAdmin'])->group(function () {

        Route::prefix('/{erp}/db')->group(function () {

            Route::get('/', 'masterDB')->name('masterDB');
            Route::get('/search', 'search')->name('searchDB');

            Route::get('/add', 'addDB')->name('addDB');
            Route::post('/add', 'storeDB')->name('storeDB');

            Route::get('/edit/{dbid}', 'editDB')->name('editDB');
            Route::put('/edit/{dbid}', 'updateDB')->name('updateDB');

            Route::delete('/delete/{dbid}', 'deleteDB')->name('deleteDB');
        });
    });
});

Route::controller(DBViewController::class)->group(function () {

    Route::middleware(['auth', 'checkAccess', 'checkPIC'])->group(function () {

        Route::prefix('/{erp}/db-view')->group(function () {

            Route::get('/', 'masterView')->name('masterView');
            Route::get('/search', 'search')->name('searchDBView');

            Route::get('/add', 'addView')->name('addView');
            Route::post('/add', 'storeView')->name('storeView');

            Route::get('/details/{id}', 'detailView')->name('detailView');
            Route::put('/edit/{id}', 'updateView')->name('updateView');
            // Route::get('/edit/{dbid}', 'editDB')->name('editDB');
            // Route::delete('/delete/{dbid}', 'deleteDB')->name('deleteDB');
        });
    });
});

Route::controller(DBFuncController::class)->group(function () {

    Route::middleware(['auth', 'checkAccess', 'checkPIC'])->group(function () {

        Route::prefix('/{erp}/db-function')->group(function () {

            Route::get('/', 'masterFunction')->name('masterFunction');
            Route::get('/search', 'search')->name('searchDBFunction');

            Route::get('/add', 'addFunction')->name('addFunction');
            Route::post('/add', 'storeFunction')->name('storeFunction');

            Route::get('/details/{id}', 'detailFunction')->name('detailFunction');
            Route::put('/edit/{id}', 'updateFunction')->name('updateFunction');
            // Route::get('/edit/{dbid}', 'editDB')->name('editDB');
            // Route::delete('/delete/{dbid}', 'deleteDB')->name('deleteDB');
        });
    });
});

Route::controller(DBStoreProcController::class)->group(function () {

    Route::middleware(['auth', 'checkAccess', 'checkPIC'])->group(function () {

        Route::prefix('/{erp}/db-store-procedure')->group(function () {

            Route::get('/', 'masterStoreProc')->name('masterStoreProc');
            Route::get('/search', 'search')->name('searchDBStoreProc');

            Route::get('/add', 'addStoreProc')->name('addStoreProc');
            Route::post('/add', 'storeStoreProc')->name('storeStoreProc');

            Route::get('/details/{id}', 'detailStoreProc')->name('detailStoreProc');
            Route::put('/edit/{id}', 'updateStoreProc')->name('updateStoreProc');
            // Route::get('/edit/{dbid}', 'editDB')->name('editDB');
            // Route::delete('/delete/{dbid}', 'deleteDB')->name('deleteDB');
        });
    });
});

Route::controller(TableController::class)->group(function () {

    Route::middleware(['auth', 'checkAccess', 'checkPIC'])->group(function () {

        Route::prefix('/{erp}/tables')->group(function () {

            Route::get('/', 'masterTable')->name("masterTable");
            Route::get('/search', 'search')->name('searchTable');

            Route::get('/detailTable/{id}', 'detailTable')->name('detailTable');
            Route::put('/editTable', 'updateTable')->name('updateTable');

            Route::post('/update-fields', 'updateFields')->name('updateFields');
            Route::post('/import-excel', 'import')->name('import.excel');
            // Route::post('/addTable', 'storeTable')->name('storeTable');
            // Route::get('/addTable', 'addTable')->name('addTable');
            // Route::get('/editTable/{id}', 'editTable')->name('editTable');
        });
    });
});

Route::controller(ModuleController::class)->group(function () {

    Route::prefix('/{erp}/modules')->group(function () {

        Route::middleware(['auth', 'checkAccess'])->group(function () {

            Route::get('/', 'masterModule')->name("masterModule");
            Route::get('/search', 'search')->name('searchModule');
            Route::get('/detailModule/{id}', 'detailModule')->name('detailModule');

            Route::middleware(['checkPIC'])->group(function () {

                Route::get('/addModule', 'addModule')->name('addModule');
                Route::post('/addModule', 'storeModule')->name('storeModule');

                Route::get('/editModule/{id}', 'editModule')->name('editModule');
                Route::put('/editModule/{id}', 'updateModule')->name('updateModule');

                Route::delete('/delete/{id}', 'deleteModule')->name('deleteModule');
            });

            Route::middleware('isAdmin')->group(function () {

                Route::get('/addCategory', 'addCategory')->name('addModuleCategory');
                Route::post('/addCategory', 'storeCategory')->name('storeModuleCategory');
                Route::delete('/delCategory/{id}', 'deleteCategory')->name('delModuleCategory');
            });

            // Route::get('/detailTable/{id}', 'detailTable')->name('detailTable');
            // Route::post('/update-fields', 'updateFields')->name('updateFields');
        });
    });
});

Route::controller(ReportController::class)->group(function () {

    Route::prefix('/{erp}/reports')->group(function () {

        Route::middleware(['auth', 'checkAccess'])->group(function () {

            Route::get('/', 'masterReport')->name("masterReport");
            Route::get('/search', 'search')->name('searchReport');
            Route::get('/detailReport/{id}', 'detailReport')->name('detailReport');

            Route::middleware(['checkPIC'])->group(function () {

                Route::get('/addReport', 'addReport')->name('addReport');
                Route::post('/addReport', 'storeReport')->name('storeReport');

                Route::get('/editReport/{id}', 'editReport')->name('editReport');
                Route::put('/editReport/{id}', 'updateReport')->name('updateReport');

                Route::delete('/delete/{id}', 'deleteReport')->name('deleteReport');
            });

            Route::middleware('isAdmin')->group(function () {

                Route::get('/addCategory', 'addCategory')->name('addReportCategory');
                Route::post('/addCategory', 'storeCategory')->name('storeReportCategory');
                Route::delete('/delCategory/{id}', 'deleteCategory')->name('delReportCategory');
            });
            // Route::get('/detailTable/{id}', 'detailTable')->name('detailTable');
            // Route::post('/update-fields', 'updateFields')->name('updateFields');
        });
    });
});

Route::get('/fetch-fields/{tableID}', [TableController::class, 'fetchFields']);
