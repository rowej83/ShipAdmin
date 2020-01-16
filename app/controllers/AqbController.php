<?php

/**
 * Created by PhpStorm.
 * User: jrowe
 * Date: 9/18/2015
 * Time: 7:22 PM
 */
class   AqbController extends \BaseController
{

    public function parseGetPDF()
    {
        return View::make('parsepdf-input');

    }

    public function parsePostPDF()
    {

        $validator = Validator::make(Input::all(),
            array(
                'packinglist' => 'required'
            )
        );
        if (!$validator->fails()) {

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
            $returnPOString = '';
            foreach ($arrayOfPos as $returnPO) {
                $returnPOString .= $returnPO . '<br>';
            }
            $data['POs'] = $returnPOString;
            $data['totalOfPOs'] = $totalOfPos;
            $data['queryString'] = $queryString;
            return View::make('parsepdf-output', $data);
        } else {
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
                return $stringresponse .= "('" . $item . "')";
            } elseif ($i == 1) {
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
            $additionalQuery='';
            $optionResult = Input::get('optionsRadios');

            switch ($optionResult) {
                case 'ordernumbers':
                    $stringresponse = $this->joinOrders($tempitems);
                    break;
                case 'shipmentnumbers':

                    $stringresponse = $this->joinShipments($tempitems, Input::get('addShipments'));
                    break;
                case 'commas':

                    $stringresponse = $this->joinCommas($tempitems, Input::get('addShipments'));
                    break;
                case 'spaces':
                    $stringresponse=$this->joinSpaces($tempitems);
                    break;
                case 'pos':
                    $stringresponse = $this->joinPurchaseOrders($tempitems);
                    break;
                case 'customers':
                    $stringresponse = $this->joinCustomers($tempitems);
                    break;
                case 'partinventoryscreen':
                    $stringresponse = $this->joinPartsInventoryScreen($tempitems);
                    break;
                case 'partoutboundscreen':
                    $stringresponse = $this->joinPartsOutBoundScreen($tempitems);
                    break;
                case 'docfetcher':
                    $stringresponse = $this->joinForDocFetcher($tempitems);
                    break;
                case 'ten-digit':
                    $getResultstoReturn=$this->confirmTenDigits($tempitems);
                    $stringresponse=$getResultstoReturn['stringresponse'];
                    $additionalQuery=$getResultstoReturn['additionalQuery'];
                    break;
                case 'amazonpos':

                    // @todo will need to refactor, should not repeat it's self
                    $tempitems = $this->prepareAmazonArray();
                    $stringresponse = $this->joinAmazonPurchaseOrders($tempitems);
                    $data['response'] = $stringresponse;
                    $data['itemCount'] = count($tempitems);
                    $data['uniqueItemCount'] = count(array_unique($tempitems));

                    return View::make('join-output', $data);

                    break;
                default:
                    //code to be executed if n is different from all labels;
                    $stringresponse = $this->simpleJoin($tempitems, Input::get('addShipments'));
            }


            $data['response'] = $stringresponse;
            $data['additionalQuery']=$additionalQuery;
            $data['itemCount'] = count($tempitems);
            $data['uniqueItemCount'] = count(array_unique($tempitems));

            return View::make('join-output', $data);

        }

    }
    private function confirmTenDigits($listOfPOs){
      $data['stringresponse']='';
      $correctedPOs=[];

        if(count($listOfPOs)!=0){

            foreach($listOfPOs as $po){
                $poLength=strlen($po);
                if($poLength<10){
                    $zerosToAddToPO='';
                    $zerosNeeded=10-$poLength;
                    for($i=0;$i<$zerosNeeded;$i++){
                        $zerosToAddToPO.='0';

                    }
                    $data["stringresponse"].=$zerosToAddToPO.$po.'<br>';
                    array_push($correctedPOs,$zerosToAddToPO.$po);
                }else{
                    $data["stringresponse"].=$po.'<br>';
                    array_push($correctedPOs,$po);
                }

            }
        }
        else{
            return 'Empty list';
        }
        $data['additionalQuery']=$this->joinPurchaseOrders($correctedPOs);
        return $data;

    }
    private function joinPartsInventoryScreen($customersArray)
    {
        $stringresponse = 'iv_f.sku in ';
        $i = 1;
        $totalitems = count($customersArray);
        foreach ($customersArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= "('" . $item . "')";
            } elseif ($i == 1) {
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

    private function joinForDocFetcher($customersArray)
    {
        //  $stringresponse = 'iv_f.sku in ';
        $stringresponse = '';
        $i = 1;
        $totalitems = count($customersArray);
        foreach ($customersArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= "*" . $item . "*";
            } elseif ($i == 1) {
                $stringresponse .= "*" . $item . "*";
            } elseif ($i == $totalitems) {
                $stringresponse .= " OR *" . $item . "*";
            } else {
                $stringresponse .= " OR *" . $item . "*";
            }
            $i++;
        }
        return $stringresponse;

    }

    private function joinPartsOutBoundScreen($customersArray)
    {
        $stringresponse = 'od_f.sku in ';
        $i = 1;
        $totalitems = count($customersArray);
        foreach ($customersArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= "('" . $item . "')";
            } elseif ($i == 1) {
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

    private function joinCustomers($customersArray)
    {
        $stringresponse = 'om_f.bill_custnum in ';
        $i = 1;
        $totalitems = count($customersArray);
        foreach ($customersArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= "('" . $item . "')";
            } elseif ($i == 1) {
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

    /*
  * Adds shipment numbers together for AQB and returns string.
   * Will also add SHP if $addSHPTest is true
   * e.g.    om_f.shipment in ('251212548','251574987')
  */

    private function joinShipments($shipmentArray, $addSHPTest)
    {

        $stringresponse = 'om_f.shipment in ';
        $i = 1;
        $shipmentArray=array_unique($shipmentArray);
         sort($shipmentArray);
        $totalitems = count($shipmentArray);
        foreach ($shipmentArray as $item) {


            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= "('" . $this->addSHP($item, $addSHPTest) . "')";
            } elseif ($i == 1) {
                $stringresponse .= "('" . $this->addSHP($item, $addSHPTest) . "',";
            } elseif ($i == $totalitems) {
                $stringresponse .= "'" . $this->addSHP($item, $addSHPTest) . "')";
            } else {
                $stringresponse .= "'" . $this->addSHP($item, $addSHPTest) . "',";
            }

            $i++;
        }
        return $stringresponse;

    }

    /*
    * Adds order numbers together for AQB and returns string
     * e.g.    om_f.ob_oid in ('251212548','251574987')
    */

    private function joinOrders($ordersArray)
    {

        $stringresponse = 'om_f.ob_oid in ';
        $i = 1;
        $ordersArray=array_unique($ordersArray);
        sort($ordersArray);
        $totalitems = count($ordersArray);
        foreach ($ordersArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= "('" . $item . "')";
            } elseif ($i == 1) {
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

    /*
   * Adds order numbers together for AQB and returns string
    * e.g.    om_f.ship_po in ('po1','po2')
   */

    private function joinPurchaseOrders($ordersArray)
    {

        $stringresponse = 'om_f.ship_po in ';
        $i = 1;
        $totalitems = count($ordersArray);
        foreach ($ordersArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= "('" . $item . "')";
            } elseif ($i == 1) {
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

    private function joinAmazonPurchaseOrders($ordersArray)
    {

        $stringresponse = 'om_f.ship_po in ';
        $i = 1;
        $totalitems = count($ordersArray);
        foreach ($ordersArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= "('" . $item . "')";
            } elseif ($i == 1) {
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
    /*
       * Creates a string such as item,item2,item3
       * Can also add SHP if $addSHPTest is true
       */
    private function joinSpaces($itemArray)
    {
        $stringresponse = '';

        $i = 1;
        $totalitems = count($itemArray);
        foreach ($itemArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= $item;

            } elseif ($i == 1) {
                $stringresponse .= $item . ' ';
            } elseif ($i == $totalitems) {
                $stringresponse .= $item;
            } else {
                $stringresponse .= $item . " ";
            }
            $i++;
        }
        return $stringresponse;

    }
    /*
     * Creates a string such as item,item2,item3
     * Can also add SHP if $addSHPTest is true
     */
    private function joinCommas($itemArray, $addSHPTest)
    {
        $stringresponse = '';

        $i = 1;
        $totalitems = count($itemArray);
        foreach ($itemArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= $this->addSHP($item, $addSHPTest);

            } elseif ($i == 1) {
                $stringresponse .= $this->addSHP($item, $addSHPTest) . ',';
            } elseif ($i == $totalitems) {
                $stringresponse .= $this->addSHP($item, $addSHPTest);
            } else {
                $stringresponse .= $this->addSHP($item, $addSHPTest) . ",";
            }
            $i++;
        }
        return $stringresponse;

    }

    /*
     * If $test is true it will add SHP to each element
     */
    function addSHP($value, $test)
    {

        if ($test == true) {
            return 'SHP' . $value;
        } else {
            return $value;
        }
    }


    /*
     * Adds elements together for AQB use only e.g. ('item1','item2')
     */
    private function simpleJoin($itemArray, $addSHPTest)
    {

        $stringresponse = '';

        $i = 1;
        $totalitems = count($itemArray);
        foreach ($itemArray as $item) {
            if ($i == 1 && $totalitems == 1) {
                return $stringresponse .= "('" . $this->addSHP($item, $addSHPTest) . "')";
            } elseif ($i == 1) {
                $stringresponse .= "('" . $this->addSHP($item, $addSHPTest) . "',";
            } elseif ($i == $totalitems) {
                $stringresponse .= "'" . $this->addSHP($item, $addSHPTest) . "')";
            } else {
                $stringresponse .= "'" . $this->addSHP($item, $addSHPTest) . "',";
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
        $badItemsToTest = ['shp#', 'totals', 'total', 'shipment', 'shipment#', ''];

        $finalArray = array();

        foreach ($items as $item) {

            if (in_array(strtolower(trim($item)), $badItemsToTest) == false) {
                array_push($finalArray, trim($item));
            }

        }
        return $finalArray;


    }

    private function prepareAmazonArray()
    {
        $tempItems = trim(Input::get('items'));
        $items = explode(',', $tempItems);

        $finalArray = array();
        foreach ($items as $item) {
            if (trim($item) != '') {
                array_push($finalArray, trim($item));
            }

        }
        return $finalArray;
  }

    public function ajaxJoinOrders()
    {
        $array = json_decode(Input::get('orders'));
        $data['resultQueryString'] = $this->joinOrders($array);
        $data['count'] = count($array);
        $data['unique'] = count(array_unique($array));
        return json_encode($data);
    }
    public function ajaxJoinSHP()
    {
        $array = json_decode(Input::get('shps'));
        $data['resultQueryString'] = $this->joinShipments($array,false);
        $data['count'] = count($array);
        $data['unique'] = count(array_unique($array));
        return json_encode($data);
    }
}