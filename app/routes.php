<?php

Route::get('/', ['as' => 'estimateHome', 'uses' => 'ItemController@index']);

Route::post('/', ['as' => 'input', 'uses' => 'ItemController@input']);
Route::get('silentReload', ['as' => 'silentReload', 'uses' => 'ItemController@silentReload']);
Route::get('aqb', ['as' => 'joinHome', 'uses' => 'AqbController@join']);
Route::post('aqb', ['as' => 'join', 'uses' => 'AqbController@performjoin']);
Route::get('deleteDB', ['as' => 'deleteDBForm', 'uses' => 'ItemController@deleteDBForm']);
Route::post('deleteDB', ['as' => 'deleteDBSubmission', 'uses' => 'ItemController@deleteDBSubmission']);

Route::get('load', ['as' => 'load', 'uses' => 'ItemController@load']);

Route::get('parsePDF', ['as' => 'parseGetPDF', 'uses' => 'AqbController@parseGetPDF']);
Route::post('parsePostPDF', ['as' => 'parsePostPDF', 'uses' => 'AqbController@parsePostPDF']);

Route::get('/parseKohlsPDF',
    ['as' => 'parseGetKohlsPDF', 'uses' => 'KohlsController@parseGetPDF']);

Route::post('/parseKohlsPDF',
    ['as' => 'parsePostKohlsPDF', 'uses' => 'KohlsController@parsePostPDF']);

Route::get('/retrieveKohlsPDF',
    ['as' => 'retrieveGetKohlsPDF', 'uses' => 'KohlsController@retrieveGetPDF']);

Route::post('/retrieveKohlsPDF',
    ['as' => 'retrievePostKohlsPDF', 'uses' => 'KohlsController@retrievePostPDF']);

Route::get('deleteKohlsDB', ['as' => 'deleteKohlsDBForm', 'uses' => 'KohlsController@deleteDBForm']);
Route::post('deleteKohlsDB', ['as' => 'deleteKohlsDBSubmission', 'uses' => 'KohlsController@deleteDBSubmission']);

Route::get('checkforground',
    ['as' => 'Getcheckforground', 'uses' => 'KohlsController@Getcheckforground']);

Route::post('checkforground',
    ['as' => 'Postcheckforground', 'uses' => 'KohlsController@Postcheckforground']);


Route::get('deleteLAndTDB', ['as' => 'deleteLAndTDBForm', 'uses' => 'LordAndTaylorController@deleteDBForm']);
Route::post('deleteLAndTDB', ['as' => 'deleteLAndTDBSubmission', 'uses' => 'LordAndTaylorController@deleteDBSubmission']);

Route::get('/parseLAndTPDF',
    ['as' => 'parseGetLAndTPDF', 'uses' => 'LordAndTaylorController@parseGetPDF']);

Route::post('/parseLAndTPDF',
    ['as' => 'parsePostLAndTPDF', 'uses' => 'LordAndTaylorController@parsePostPDF']);

Route::get('/retrieveLAndTPDF',
    ['as' => 'retrieveGetLAndTPDF', 'uses' => 'LordAndTaylorController@retrieveGetPDF']);

Route::post('/retrieveLAndTPDF',
    ['as' => 'retrievePostLAndTPDF', 'uses' => 'LordAndTaylorController@retrievePostPDF']);

//macys below

Route::get('deleteMacysDB', ['as' => 'deleteMacysDBForm', 'uses' => 'MacysController@deleteDBForm']);
Route::post('deleteMacysDB', ['as' => 'deleteMacysDBSubmission', 'uses' => 'MacysController@deleteDBSubmission']);

Route::get('/parseMacysPDF',
    ['as' => 'parseGetMacysPDF', 'uses' => 'MacysController@parseGetPDF']);

Route::post('/parseMacysPDF',
    ['as' => 'parsePostMacysPDF', 'uses' => 'MacysController@parsePostPDF']);

Route::get('/retrieveMacysPDF',
    ['as' => 'retrieveGetMacysPDF', 'uses' => 'MacysController@retrieveGetPDF']);

Route::post('/retrieveMacysPDF',
    ['as' => 'retrievePostMacysPDF', 'uses' => 'MacysController@retrievePostPDF']);

    Route::get('/descrambledo', ['as' => 'descrambledo', function(){
        return View::make('do-descrambler');
    }]);


Route::post('/ajaxJoinOrders',['as'=>'ajaxJoinOrders','uses'=>'AqbController@ajaxJoinOrders']);


Route::get('amazoncsv',['as'=>'amazoncsvGET','uses'=>'AmazonController@getPreRouting']);
Route::post('amazoncsv',['as'=>'amazoncsvPOST','uses'=>'AmazonController@postPreRouting']);

Route::get('testcsv', function () {

});

Route::get('getChargeBack',['as'=>'ChargeBackGET','uses'=>'ChargeBackController@getChargeBack']);

Route::post('postChargeBack',['as'=>'ChargeBackPOST','uses'=>'ChargeBackController@postChargeBack']);