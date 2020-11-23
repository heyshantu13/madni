<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register'=>false]);




Route::group(['middleware' => 'auth'], function()  
{ 

    // User routes

    Route::get('/user_logout','DashboardController@logout' )->name('dashboard.logout');
    Route::get('/','HomeController@index' )->name('home.index');


Route::get('/dashboard','DashboardController@index' )->name('dashboard.index');
// Route::get('/customers','CustomersController@index' );


// Customers Route::

Route::group(['prefix' => 'customers'], function()  
{  
   Route::get('/','CustomersController@index')->name('customers.index');
   Route::get('/create','CustomersController@create')->name('customers.create');
   Route::post('/create','CustomersController@store')->name('customers.store');
   Route::get('/edit/{id}','CustomersController@edit')->name('customers.edit');
   Route::post('/edit/{id}','CustomersController@update')->name('customers.update');
   Route::get('/delete/{id}','CustomersController@delete')->name('customers.delete');

});  


Route::group(['prefix' => 'price_category'], function()  
{  
   Route::get('/','PriceCategoriesController@index')->name('pricecategory.index');
   Route::get('/create','PriceCategoriesController@create')->name('pricecategory.create');
   Route::post('/create','PriceCategoriesController@store')->name('pricecategory.store');
   Route::get('/edit/{id}','PriceCategoriesController@edit')->name('pricecategory.edit');
   Route::post('/update/{id}','PriceCategoriesController@update')->name('pricecategory.update');
   Route::get('/delete/{id}','PriceCategoriesController@delete')->name('pricecategory.delete');

});  


Route::group(['prefix' => 'invoice'], function()  
{  
   Route::get('/','InvoiceController@index')->name('invoice.index');
   Route::get('/create/{id?}','InvoiceController@create')->name('invoice.create');
   Route::get('/new/invoice','InvoiceController@newInvoice')->name('invoice.new');
   Route::post('/generate','InvoiceController@store')->name('invoice.store');
   Route::get('/edit/{id}','InvoiceController@edit')->name('invoice.edit');
   Route::post('/update/{id}','InvoiceController@update')->name('invoice.update');
   Route::get('/delete/{id}','InvoiceController@delete')->name('invoice.delete');
   Route::get('/view-lists/{customerid}','InvoiceController@viewlists')->name('invoice.view');
   Route::get('pdf/invoice/download/{id?}','InvoiceController@generateInvoicePdf')->name('invoice.download');

});  

Route::get('/getCalculateAmount','ApiMadniController@calculateAmount');
Route::post('/createInvoiceByAjax','ApiMadniController@generateInvoice');
Route::get('/getCalculateAmountFedex','ApiMadniController@calculateFedexPrice')->name('fedex.calculte');


});

// Customers Route::End



Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

// 404 for undefined routes
Route::any('/{page?}',function(){
    return View::make('pages.error.404');
})->where('page','.*');



Route::get('/home', 'HomeController@index')->name('home');
