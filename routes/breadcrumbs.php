<?php

use App\Models\DBFunction;
use App\Models\DBStoreProc;
use App\Models\DBView;
use App\Models\Module;
use App\Models\Report;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;


Breadcrumbs::for('erpMenu', function ($trail, $erp) {
    $trail->push('Menu ' . $erp, route('erpMenu', $erp));
});


// DATABASE
Breadcrumbs::for('masterDB', function ($trail, $erp) {
    $trail->parent('erpMenu', $erp);
    $trail->push('Database ' . $erp, route('masterDB', $erp));
});

Breadcrumbs::for('addDB', function (BreadcrumbTrail $trail, $erp): void {
    $trail->parent('masterDB', $erp);

    $trail->push('Tambah Database ' . $erp, route('addDB', $erp));
});

Breadcrumbs::for('editDB', function (BreadcrumbTrail $trail, $erp, $dbid): void {
    $trail->parent('masterDB', $erp);

    $trail->push('Edit Database ' . $erp, route('editDB', [$erp, $dbid]));
});

Breadcrumbs::for('searchDB', function ($trail, $erp): void {
    $trail->parent('masterDB', $erp);
    $trail->push('Searched Database', route('searchDB', $erp));
});



// TABLE DOCUMENTATION
Breadcrumbs::for('masterTable', function ($trail, $erp) {
    $trail->parent('erpMenu', $erp);
    $trail->push('Tabel ' . $erp, route('masterTable', $erp));
});

Breadcrumbs::for('detailTable', function (BreadcrumbTrail $trail, $erp, $id): void {

    $trail->parent('masterTable', $erp);
    $trail->push('Detail Tabel', route('detailTable', [$erp, $id]));
});

Breadcrumbs::for('searchTable', function ($trail, $erp): void {
    $trail->parent('masterTable', $erp);
    $trail->push('Searched Table', route('searchTable', $erp));
});

// Breadcrumbs::for('editDB', function (BreadcrumbTrail $trail, $erp, $dbid): void {
//     $trail->parent('masterDB', $erp);

//     $trail->push('Edit ' . $erp, route('editDB', [$erp, $dbid]));
// });

// CATEGORIES
Breadcrumbs::for('addModuleCategory', function ($trail, $erp) {
    $trail->parent('erpMenu', $erp);
    $trail->push('Tambah Kategori Modul ' . $erp, route('addModuleCategory', $erp));
});

Breadcrumbs::for('addReportCategory', function ($trail, $erp) {
    $trail->parent('erpMenu', $erp);
    $trail->push('Tambah Kategori Report ' . $erp, route('addReportCategory', $erp));
});


// MODULE DOCUMENTATION
Breadcrumbs::for('masterModule', function ($trail, $erp) {
    $trail->parent('erpMenu', $erp);
    $trail->push('Modul ' . $erp, route('masterModule', $erp));
});

Breadcrumbs::for('addModule', function (BreadcrumbTrail $trail, $erp): void {
    $trail->parent('masterModule', $erp);

    $trail->push('Tambah Modul ' . $erp, route('addModule', $erp));
});

Breadcrumbs::for('editModule', function (BreadcrumbTrail $trail, $erp, $id): void {
    $trail->parent('masterModule', $erp);
    Module::find($id)->first()->Name;
    $trail->push('Edit Modul ' . Module::find($id)->first()->Name, route('editModule', [$erp, $id]));
});

Breadcrumbs::for('detailModule', function (BreadcrumbTrail $trail, $erp, $id): void {
    $trail->parent('masterModule', $erp);
    $trail->push('Modul ' . Module::find($id)->first()->Name, route('detailModule', [$erp, $id]));
});

Breadcrumbs::for('searchModule', function ($trail, $erp): void {
    $trail->parent('masterModule', $erp);
    $trail->push('Searched Module', route('searchModule', $erp));
});

// REPORT DOCUMENTATION
Breadcrumbs::for('masterReport', function ($trail, $erp) {
    $trail->parent('erpMenu', $erp);
    $trail->push('Report ' . $erp, route('masterReport', $erp));
});

Breadcrumbs::for('addReport', function (BreadcrumbTrail $trail, $erp): void {
    $trail->parent('masterReport', $erp);

    $trail->push('Tambah Report ' . $erp, route('addReport', $erp));
});

Breadcrumbs::for('editReport', function (BreadcrumbTrail $trail, $erp, $id): void {
    $trail->parent('masterReport', $erp);
    Report::find($id)->first()->Name;
    $trail->push('Edit Report ' . Report::find($id)->first()->Name, route('editReport', [$erp, $id]));
});

Breadcrumbs::for('detailReport', function (BreadcrumbTrail $trail, $erp, $id): void {
    $trail->parent('masterReport', $erp);
    $trail->push('Report ' . Report::find($id)->first()->Name, route('detailReport', [$erp, $id]));
});

Breadcrumbs::for('searchReport', function ($trail, $erp): void {
    $trail->parent('masterModule', $erp);
    $trail->push('Searched Module', route('searchReport', $erp));
});


// USER MANAGEMENT
Breadcrumbs::for('dashboard', function ($trail,) {
    $trail->push('Dashboard', route('dashboard'));
});

Breadcrumbs::for('showPswChange', function ($trail, $id) {
    $trail->parent('dashboard');
    $trail->push('Ganti Password', route('showPswChange', $id));
});

Breadcrumbs::for('userList', function ($trail,) {
    $trail->parent('dashboard');
    $trail->push('List User', route('userList'));
});

Breadcrumbs::for('registerUser', function ($trail,) {
    $trail->parent('userList');
    $trail->push('Tambah User', route('registerUser'));
});

Breadcrumbs::for('editUser', function ($trail, $id) {
    $trail->parent('userList');
    $trail->push('Edit User', route('editUser', $id));
});

Breadcrumbs::for('user.search', function ($trail): void {
    $trail->parent('userList');
    $trail->push('Searched User', route('userList'));
});

Breadcrumbs::for('filter.users', function ($trail): void {
    $trail->parent('userList');
    $trail->push('Filtered User', route('userList'));
});


// DB VIEWS
Breadcrumbs::for('masterView', function ($trail, $erp) {
    $trail->parent('erpMenu', $erp);
    $trail->push('Database View ' . $erp, route('masterView', $erp));
});

Breadcrumbs::for('addView', function (BreadcrumbTrail $trail, $erp): void {
    $trail->parent('masterView', $erp);

    $trail->push('Tambah Database View ' . $erp, route('addView', $erp));
});

Breadcrumbs::for('detailView', function (BreadcrumbTrail $trail, $erp, $id): void {
    $trail->parent('masterView', $erp);
    $trail->push('Database View ' . DBView::find($id)->first()->Name, route('detailView', [$erp, $id]));
});

Breadcrumbs::for('searchDBView', function ($trail, $erp): void {
    $trail->parent('masterView', $erp);
    $trail->push('Searched DB View', route('searchDBView', $erp));
});

// DB FUNCTION
Breadcrumbs::for('masterFunction', function ($trail, $erp) {
    $trail->parent('erpMenu', $erp);
    $trail->push('Database Function ' . $erp, route('masterFunction', $erp));
});

Breadcrumbs::for('addFunction', function (BreadcrumbTrail $trail, $erp): void {
    $trail->parent('masterFunction', $erp);

    $trail->push('Tambah Database Function ' . $erp, route('addFunction', $erp));
});

Breadcrumbs::for('detailFunction', function (BreadcrumbTrail $trail, $erp, $id): void {
    $trail->parent('masterFunction', $erp);
    $trail->push('Database Function ' . DBFunction::find($id)->first()->Name, route('detailFunction', [$erp, $id]));
});

Breadcrumbs::for('searchDBFunction', function ($trail, $erp): void {
    $trail->parent('masterFunction', $erp);
    $trail->push('Searched DB Function', route('searchDBFunction', $erp));
});


// DB STORE PROCEDURE
Breadcrumbs::for('masterStoreProc', function ($trail, $erp) {
    $trail->parent('erpMenu', $erp);
    $trail->push('Database Store Procedure ' . $erp, route('masterStoreProc', $erp));
});

Breadcrumbs::for('addStoreProc', function (BreadcrumbTrail $trail, $erp): void {
    $trail->parent('masterStoreProc', $erp);

    $trail->push('Tambah Database Store Procedure ' . $erp, route('addStoreProc', $erp));
});

Breadcrumbs::for('detailStoreProc', function (BreadcrumbTrail $trail, $erp, $id): void {
    $trail->parent('masterStoreProc', $erp);
    $trail->push('Database Store Procedure ' . DBStoreProc::find($id)->first()->Name, route('detailStoreProc', [$erp, $id]));
});

Breadcrumbs::for('searchDBStoreProc', function ($trail, $erp): void {
    $trail->parent('masterFunction', $erp);
    $trail->push('Searched DB Store Procedure', route('searchDBStoreProc', $erp));
});
