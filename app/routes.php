<?php

Route::get('/', ['uses'=>'ItemController@index']);

Route::post('/',['as'=>'input','uses'=>'ItemController@input']);

Route::get('aqb', ['as'=>'joinHome','uses'=>'AqbController@join']);
Route::post('aqb', ['as'=>'join','uses'=>'AqbController@performjoin']);

Route::get('load', ['uses'=>'ItemController@load']);


