<?php

use App\Http\Controllers\AdvertController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\AlltvController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataPinController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\EkectController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TransController;
use Illuminate\Support\Facades\Auth;
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
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'dash'])->name('dashboard');
//    Route::get('account', [DashboardController::class, 'dash'])->name('account');

//    airtime route
    Route::get('airtime', [DashboardController::class, 'airtimeindex'])->name('airtime');
    Route::post('buyairtime', [AirtimeController::class, 'buyairtime'])->name('buyairtime');

//    data route
    Route::get('pick', [DashboardController::class, 'dataindex'])->name('pick');
    Route::get('getOptions/{selectedValue}', [DashboardController::class, 'netwplanrequest'])->name('getOptions');
    Route::get('select/{request}', [DashboardController::class, 'picknetwork'])->name('select');
    Route::post('buydata', [BillController::class, 'bill'])->name('buydata');
    Route::post('buydata1', [\App\Http\Controllers\EasyaccessDataController::class, 'sellfromeasyaccess'])->name('buydata1');

//    datapin route
    Route::get('datapin', [DataPinController::class, 'dataindex'])->name('datapin');
    Route::post('buypin', [DataPinController::class, 'processdatapin'])->name('buypin');


//    tv route
    Route::get('listtv', [AlltvController::class, 'listtv'])->name('listtv');
    Route::view('tv', 'bills.tv');
    Route::get('picktv/{selectedValue}', [AlltvController::class, 'tv'])->name('picktv');
    Route::get('verifytv/{value1}/{value2}', [AlltvController::class, 'verifytv'])->name('verifytv');
    Route::post('buytv', [AlltvController::class, 'paytv'])->name('buytv');

//    electricity route
    Route::get('listelect', [EkectController::class, 'listelect'])->name('listelect');
    Route::get('electricity', [EkectController::class, 'electric'])->name('electricity');
    Route::get('verifyelec/{value1}/{value2}', [EkectController::class, 'verifyelect'])->name('verifyelec');
    Route::post('buyelect', [EkectController::class, 'payelect'])->name('buyelect');

//    bills invoice route
    Route::get('invoice', [DashboardController::class, 'invoice'])->name('invoice');
    Route::get('viewpdf/{id}', [PdfController::class, 'viewpdf'])->name('viewpdf');
    Route::get('/dopdf/{id}', [PdfController::class, 'dopdf'])->name('dopdf');

//    education route
    Route::get('listedu', [EducationController::class, 'listedu'])->name('listedu');
    Route::get('neco', [EducationController::class, 'indexn'])->name('neco');
    Route::get('waec', [EducationController::class, 'indexw'])->name('waec');
    Route::post('buyneco', [EducationController::class, 'neco'])->name('buyneco');
    Route::post('buywaec', [EducationController::class, 'waec'])->name('buywaec');

//    transaction route
    Route::get('notification', [NotificationController::class, 'loadtransaction'])->name('notification');
    Route::get('clearall', [NotificationController::class, 'cleartransaction'])->name('clearall');
    Route::get('deposit', [FundController::class,'fund'])->name('deposit');

//    task route
    Route::get('tasks', [TaskController::class, 'loadalltask'])->name('tasks');
    Route::get('advert', [TransController::class, 'alladvert'])->name('advert');
    Route::get('myads', [AdvertController::class, 'myadsload'])->name('myads');
    Route::post('padvert', [AdvertController::class, 'advert'])->name('padvert');
    Route::get('ads-detail/{id}', [AdvertController::class, 'adsdetails'])->name('ads-detail');


//    spin route
    Route::get('spin', [\App\Http\Controllers\SpinController::class, 'loadspin'])->name('spin');

    Route::get('/transaction', [DashboardController::class, 'getTransactions']);
    Route::get('/transaction1', [DashboardController::class, 'getTransactions1']);

//    upgrade route
    Route::get('reseller', [ResellerController::class, 'sell'])->name('reseller');
    Route::get('upgrade', [ResellerController::class, 'apiaccess'])->name('upgrade');
    Route::post('mp', [ResellerController::class, 'reseller'])->name('mp');
    Route::view('vtu', 'reseller.vtu');

});
Route::get('/logout', function(){
    Auth::logout();
    return view('auth.login')->with('success', 'Logout Successful');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [DashboardsController::class, 'dashboard'])->name('admin/dashboard');
    Route::get('admin/pd2/{id}', [ProductController::class, 'on2'])->name('admin/pd2');
    Route::get('admin/user', [UsersController::class, 'index'])->name('admin/user');
    Route::get('admin/deposits', [TransactionController::class, 'in'])->name('admin/deposits');
    Route::get('admin/request', [WithadController::class, 'index'])->name('admin/request');
    Route::get('admin/approved/{id}', [WithadController::class, 'approve'])->name('admin/approved');
    Route::get('admin/disapproved/{id}', [WithadController::class, 'disapprove'])->name('admin/disapproved');
    Route::get('admin/done/{id}', [\App\Http\Controllers\Marktransaction::class, 'accepttransaction'])->name('admin/done');
    Route::get('admin/bills', [TransactionController::class, 'bill'])->name('admin/bills');
    Route::get('admin/giveaway', [BonusController::class, 'giveawayall'])->name('admin/giveaway');
    Route::get('admin/claim', [BonusController::class, 'claimby'])->name('admin/claim');
    Route::get('admin/finddeposite', [TransactionController::class, 'index'])->name('admin/finddeposite');
    Route::post('admin/depo', [TransactionController::class, 'finduser'])->name('admin/depo');
    Route::post('admin/date', [QueryController::class, 'querydeposi'])->name('admin/date');
    Route::post('admin/datebill', [QueryController::class, 'querybilldate'])->name('admin/datebill');
    Route::get('admin/depositquery', [QueryController::class, 'queryindex'])->name('admin/depositquery');
    Route::get('admin/billquery', [QueryController::class, 'billdate'])->name('admin/billquery');
    Route::get('admin/server', [UsersController::class, 'server'])->name('admin/server');

    Route::get('admin/server', [UsersController::class, 'server'])->name('admin/server');
    Route::get('admin/noti', [UsersController::class, 'mes'])->name('admin/noti');
    Route::get('admin/air', [ProductController::class, 'air'])->name('admin/air');

    Route::get('admin/up/{id}', [UsersController::class, 'up'])->name('admin/up');
    Route::get('admin/up1/{id}', [ProductController::class, 'pair'])->name('admin/up1');
    Route::get('admin/profile/{username}', [UsersController::class, 'profile'])->name('admin/profile');
//    Route::get('admin/delete/{id}', [UserController::class, 'deleteuser'])->name('admin/delete');

    Route::get('admin/viewpdf/{id}', [AdminpdfController::class, 'viewpdf'])->name('admin/viewpdf');
    Route::get('admin/dopdf/{id}', [AdminpdfController::class, 'dopdf'])->name('admin/dopdf');

    Route::get('admin/webbook', [DashboardController::class, 'webbook'])->name('admin/webbook');
    Route::get('admin/vertual', [VertualAController::class, 'list'])->name('admin/vertual');
    Route::post('admin/update', [VertualAController::class, 'updateuser'])->name('admin/update');
    Route::post('admin/pass', [VertualAController::class, 'pass'])->name('admin/pass');
    Route::get('admin/credit', [CandCController::class, 'cr'])->name('admin/credit');
    Route::post('admin/cr', [CandCController::class, 'credit'])->name('admin/cr');
    Route::post('admin/ref', [CandCController::class, 'refund'])->name('admin/ref');
    Route::post('admin/ch', [CandCController::class, 'charge'])->name('admin/ch');
    Route::post('admin/finduser', [UsersController::class, 'finduser'])->name('admin/finduser');
    Route::get('admin/finds', [UsersController::class, 'fin'])->name('admin/finds');

    Route::get('admin/regen1/{id}', [\App\Http\Controllers\admin\RegenerateVirtualAccountController::class, 'regenrateaccount1'])->name('admin/regen1');


    Route::get('admin/product', [productController::class, 'index'])->name('admin/product');
    Route::get('admin/product1', [productController::class, 'index1'])->name('admin/product1');
    Route::get('admin/product2', [productController::class, 'index2'])->name('admin/product2');
//    Route::post('admin/do', [McdController::class, 'edit'])->name('admin/do');
    Route::post('admin/do', [ProductController::class, 'edit'])->name('admin/do');
    Route::post('admin/do1', [ProductController::class, 'edit1'])->name('admin/do1');
    Route::post('admin/do2', [ProductController::class, 'edit2'])->name('admin/do2');
    Route::post('admin/not', [UsersController::class, 'me'])->name('admin/not');
    Route::get('admin/editproduct1/{id}', [ProductController::class, 'in1'])->name('admin/editproduct1');
    Route::get('admin/editproduct2/{id}', [ProductController::class, 'in2'])->name('admin/editproduct2');
    Route::get('admin/editproduct/{id}', [ProductController::class, 'in'])->name('admin/editproduct');
    Route::get('admin/pd/{id}', [ProductController::class, 'on'])->name('admin/pd');
    Route::get('admin/pd1/{id}', [ProductController::class, 'on1'])->name('admin/pd1');



    Route::get('admin/cserver', [\App\Http\Controllers\admin\CreateServerController::class, 'createnewserver'])->name('admin/cserver');
    Route::get('admin/dserver', [\App\Http\Controllers\admin\CreateServerController::class, 'createnewserver1'])->name('admin/dserver');
    Route::post('admin/cserver1', [\App\Http\Controllers\admin\CreateServerController::class, 'postnewserver'])->name('admin/cserver1');
    Route::post('admin/dserver1', [\App\Http\Controllers\admin\CreateServerController::class, 'postnewserver1'])->name('admin/dserver1');
});


Route::get('/cover/{filename}', function ($filename) {
    $path = storage_path('app/cover/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('cover');
Route::get('/profile/{filename}', function ($filename ) {
    $path = storage_path('app/profile/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('profile');
Route::get('/banner0/{filename}', function ($filename) {
    $path = storage_path('app/banner0/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('banner0');
Route::get('/app/{filename}', function ($filename) {
    $path = storage_path('app/myapp/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('app');
Route::view('policy', 'policy');
