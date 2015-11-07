<?php

/**
 * Created by PhpStorm.
 * User: jrowe
 * Date: 9/18/2015
 * Time: 7:22 PM
 */
class   AqbController extends \BaseController
{

    public function parseGetPDF(){
        return View::make('parsepdf-input');

    }

    public function parsePostPDF(){

        $validator = Validator::make(Input::all(),
            array(
                'packinglist' => 'required'
            )
        );
        if(!$validator->fails()) {

            //validation passes
            $file = Input::file('packinglist');
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file);
            $pages = $pdf->getPages();
            $arrayOfPos = array();

            foreach ($pages as $page) {
                $text = nl2br($page->getText());
                $tempPDF = explode('<br />', $text);
                $getPO = explode(':', $tempPDF[10]);
                $PO = trim($getPO[1]);
                array_push($arrayOfPos, $PO);

            }
            $totalOfPos = count($arrayOfPos);
            $queryString = $this->joinKohlsParsePO($arrayOfPos);
        //    $data['POs'] = $arrayOfPos;
            $returnPOString='';
            foreach($arrayOfPos as $returnPO){
                $returnPOString.=$returnPO.'<br>';
            }
            $data['POs']=$returnPOString;
            $data['totalOfPOs'] = $totalOfPos;
            $data['queryString'] = $queryString;
            return View::make('parsepdf-output',$data);
        }else{
//validation fails
            return View::make('parsepdf-input')->with(array('response' => '<p style="color:red;">Please select a packing list pdf to parse.</p>'));

        }


    }

    private function joinKohlsParsePO($ordersArray)
    {

        $stringresponse = 'om_f.ship_po in ';
        $i = 1;
        $totalitems = count($ordersArray);
        foreach ($ordersArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return     $stringresponse .= "('" . $item . "')";
            }
            elseif ($i == 1) {
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
            Session::flash('items', trim(Input::get('items')));

          //  $totalitems = count($tempitems);
            $i = 1;
            $stringresponse = '';
            $optionResult = Input::get('optionsRadios');

            switch ($optionResult) {
                case 'ordernumbers':
                    $stringresponse = $this->joinOrders($tempitems);
                    break;
                case 'shipmentnumbers':

                    $stringresponse = $this->joinShipments($tempitems, Input::get('addShipments'));
                    break;
                case 'commas':

                    $stringresponse = $this->joinCommas($tempitems,Input::get('addShipments'));
                    break;


                default:
                    //code to be executed if n is different from all labels;
                    $stringresponse = $this->simpleJoin($tempitems, Input::get('addShipments'));
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
            $data['response']=$stringresponse;
            $data['itemCount']=count($tempitems);
//            return View::make('join-output')->with(array('response' => $stringresponse));
            return View::make('join-output',$data);

        }

    }

    private function joinShipments($shipmentArray, $addSHPTest)
    {

        $stringresponse = 'om_f.shipment in ';
        $i = 1;
        $totalitems = count($shipmentArray);
        foreach ($shipmentArray as $item) {



                if ($i == 1 && $totalitems == 1) {
                    return $stringresponse .= "('" . $this->addSHP($item,$addSHPTest) . "')";
                } elseif ($i == 1) {
                    $stringresponse .= "('" . $this->addSHP($item,$addSHPTest) . "',";
                } elseif ($i == $totalitems) {
                    $stringresponse .= "'" . $this->addSHP($item,$addSHPTest) . "')";
                } else {
                    $stringresponse .= "'" . $this->addSHP($item,$addSHPTest) . "',";
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
            if ($i == 1 && $totalitems == 1) {
           return     $stringresponse .= "('" . $item . "')";
            }
            elseif ($i == 1) {
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

    private function joinCommas($itemArray,$addSHPTest)
    {
        $stringresponse = '';

        $i = 1;
        $totalitems = count($itemArray);
        foreach ($itemArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= $this->addSHP($item,$addSHPTest);

            }
            elseif ($i == 1) {
                $stringresponse .= $this->addSHP($item,$addSHPTest).',';
            } elseif ($i == $totalitems) {
                $stringresponse .= $this->addSHP($item,$addSHPTest);
            } else {
                $stringresponse .= $this->addSHP($item,$addSHPTest) . ",";
            }
            $i++;
        }
        return $stringresponse;

    }
 function addSHP($value, $test){

   if($test==true){
    return 'SHP'.$value;}
    else{
        return $value;
    }
}
    private function simpleJoin($itemArray,$addSHPTest)
    {

        $stringresponse = '';

        $i = 1;
        $totalitems = count($itemArray);
        foreach ($itemArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return      $stringresponse .= "('" . $this->addSHP($item,$addSHPTest) . "')";
            }
            elseif ($i == 1) {
                $stringresponse .= "('" . $this->addSHP($item,$addSHPTest) . "',";
            } elseif ($i == $totalitems) {
                $stringresponse .= "'" . $this->addSHP($item,$addSHPTest) . "')";
            } else {
                $stringresponse .= "'" . $this->addSHP($item,$addSHPTest) . "',";
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