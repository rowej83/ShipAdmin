<?php

Route::get('/', ['as'=>'estimateHome','uses'=>'ItemController@index']);

Route::post('/',['as'=>'input','uses'=>'ItemController@input']);
Route::get('silentReload',['as'=>'silentReload','uses'=>'ItemController@silentReload']);
Route::get('aqb', ['as'=>'joinHome','uses'=>'AqbController@join']);
Route::post('aqb', ['as'=>'join','uses'=>'AqbController@performjoin']);
Route::get('deleteDB',['as'=>'deleteDBForm','uses'=>'ItemController@deleteDBForm']);
Route::post('deleteDB',['as'=>'deleteDBSubmission','uses'=>'ItemController@deleteDBSubmission']);

Route::get('load', ['as'=>'load','uses'=>'ItemController@load']);

Route::get('parsePDF',['as'=>'parseGetPDF','uses'=>'AqbController@parseGetPDF']);
Route::post('parsePostPDF',['as'=>'parsePostPDF','uses'=>'AqbController@parsePostPDF']);


