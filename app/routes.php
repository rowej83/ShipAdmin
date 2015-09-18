<?php

Route::get('/', ['uses'=>'ItemController@index']);

Route::post('/',['as'=>'input','uses'=>'ItemController@input']);

Route::get('aqb', ['as'=>'joinHome','uses'=>'ItemController@join']);
Route::post('aqb', ['as'=>'join','uses'=>'ItemController@performjoin']);

Route::get('load', ['uses'=>'ItemController@load']);


