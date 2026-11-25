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

//ホーム画面
Route::get('/', 'MercariController@index');

//商品情報画面
Route::get('/item', 'MercariController@itemList');

//履歴画面
Route::get('/history/{item_id}', 'MercariController@historyList');

//詳細画面
Route::get('/detail/{item_id}', 'MercariController@detail');

//編集画面
Route::get('/edit/{item_id}', 'MercariController@editList');

//編集処理
Route::post('/editComplet', 'MercariController@editComplet');

//削除処理
Route::post('/delete/{item_id}', 'MercariController@delete')->name('item.delete');

//商品在庫数追加画面
Route::get('/stockAdd/{item_id}', 'MercariController@stockAdd');

//商品在庫数追加処理
Route::post('/addStockComplet', 'MercariController@addStockComplet');

//注文情報画面
Route::get('/order', 'MercariController@orderList');

//商品登録画面
Route::get('/addItem', 'MercariController@addItem');

//商品登録処理
Route::post('/addComplet', 'MercariController@addItemInsert');

//mercari注文管理画面
Route::get('/mercariOrder', 'MercariController@mList');

//チャート画面
Route::get('/chart', 'MercariController@chart');

//ゲストログイン
Route::get('guest', 'Auth\LoginController@guestLogin')->name('login.guest');

//クローズドサイトにするため登録機能を停止
Auth::routes([
    'register' => false, //ユーザ登録画面の無効
    'reset' => false, //パスワードリセットの無効
]);

Route::get('/home', 'HomeController@index')->name('home');
