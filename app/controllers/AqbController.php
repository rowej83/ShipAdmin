<?php

/**
 * Created by PhpStorm.
 * User: jrowe
 * Date: 9/18/2015
 * Time: 7:22 PM
 */
class AqbController extends \BaseController
{
    public function join()
    {

        return View::make('join-input');

    }

    public function performjoin()
    {
        $validator = Validator::make(Input::all(),
            array(
                'items' => 'required'
            )
        );
        if ($validator->fails()) {
            return View::make('join-input')->with(array('response' => '<p style="color:red;">Please check your input and try again.</p>'));
        } else {

            $tempitems = $this->prepareArray();
            Session::flash('items',trim(Input::get('items'))    );

            $totalitems = count($tempitems);
            $i = 1;
            $stringresponse = '';
            $optionResult = Input::get('optionsRadios');

            switch ($optionResult) {
                case 'ordernumbers':
                    $stringresponse = $this->joinOrders($tempitems);
                    break;
                case 'shipmentnumbers':

                    $stringresponse = $this->joinShipments($tempitems, Input::get('addShipments') );
                    break;


                default:
                    //code to be executed if n is different from all labels;
                    $stringresponse = $this->simpleJoin($tempitems);
            }

//            if ($optionResult == 'ordernumbers') {
//                $stringresponse .= 'om_f.ob_oid in ';
//            } elseif ($optionResult == 'shipmentnumbers') {
//                $stringresponse .= 'om_f.shipment in ';
//            }
//            if ($totalitems == 1) {
//                $stringresponse .= "('" . $items[0] . "')";
//            } else {
//                foreach ($items as $item) {
//                    if ($i == 1) {
//                        $stringresponse .= "('" . $item . "',";
//                    } elseif ($i == $totalitems) {
//                        $stringresponse .= "'" . $item . "')";
//                    } else {
//                        $stringresponse .= "'" . $item . "',";
//                    }
//                    $i++;
//                }
//            }
            return View::make('join-output')->with(array('response' => $stringresponse));

        }

    }

    private function joinShipments($shipmentArray, $addSHP)
    {

        $stringresponse = 'om_f.shipment in ';
        $i = 1;
        $totalitems = count($shipmentArray);
        foreach ($shipmentArray as $item) {

            if ($addSHP != true) {
                if ($i == 1) {
                    $stringresponse .= "('" . $item . "',";
                } elseif ($i == $totalitems) {
                    $stringresponse .= "'" . $item . "')";
                } else {
                    $stringresponse .= "'" . $item . "',";
                }
            } else {
                if ($i == 1) {
                    $stringresponse .= "('" . 'SHP' . $item . "',";
                } elseif ($i == $totalitems) {
                    $stringresponse .= "'" . 'SHP' . $item . "')";
                } else {
                    $stringresponse .= "'" . 'SHP' . $item . "',";
                }
            }
            $i++;
        }
        return $stringresponse;

    }

    private function joinOrders($ordersArray)
    {

        $stringresponse = 'om_f.ob_oid in ';
        $i = 1;
        $totalitems = count($ordersArray);
        foreach ($ordersArray as $item) {

            if ($i == 1) {
                $stringresponse .= "('" . $item . "',";
            } elseif ($i == $totalitems) {
                $stringresponse .= "'" . $item . "')";
            } else {
                $stringresponse .= "'" . $item . "',";
            }
            $i++;
        }
        return $stringresponse;

    }

    private function simpleJoin($itemArray)
    {

        $stringresponse = '';

        $i = 1;
        $totalitems = count($itemArray);
        foreach ($itemArray as $item) {

            if ($i == 1) {
                $stringresponse .= "('" . $item . "',";
            } elseif ($i == $totalitems) {
                $stringresponse .= "'" . $item . "')";
            } else {
                $stringresponse .= "'" . $item . "',";
            }
            $i++;
        }
        return $stringresponse;
    }


    /**
     *
     * Breaks up array by line (also trims) and removes empty lines)
     */
    private function prepareArray()
    {
        $tempItems = trim(Input::get('items'));
        $items = explode(PHP_EOL, $tempItems);

        $finalArray = array();
        foreach ($items as $item) {
            if (trim($item) != '') {
                array_push($finalArray, trim($item));
            }

        }
        return $finalArray;


    }
}