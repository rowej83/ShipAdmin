<?php

class AmazonController extends \BaseController
{

function getPreRouting(){
    return View::make('parseAmazonCsv-input');
}

    function postPreRouting(){
        $outputString='<div class="CSSTableGenerator"><table id="table"><tr><td>PO</td><td>ARN</td><td>Carrier ARN</td><td>Carrier</td><td>Shipment Type</td><td>Ship to</td></tr>';

        if(Input::file('amazonCSV')!=NULL){
                //validation passes
            $file = Input::file('amazonCSV');

            Excel::load($file,function($reader) use     (&$outputString){


                $items=$reader->all();
                foreach ($items as $item) {

                    if(hasComma($item->purchase_orders)){
                        $pos = explode(',', $item->purchase_orders);
                        foreach($pos as $po){
                          $outputString.='<tr><td>'.$po.'</td>';
                            $outputString.='<td>'.$item->shipment_id_arn.'</td>';
                            $outputString.='<td>'.$item->carrier_request_id.'</td>';
                            if($item->carrier){
                                //has carrier listed, output it
                                $outputString.='<td>'.$item->carrier.'</td>';
                            }else{
                                $outputString.='<td>NA</td>';
                            }
                            if($item->shipment_type){
                                //has carrier listed, output it
                                $outputString.='<td>'.$item->shipment_type.'</td>';
                            }else{
                                $outputString.='<td>NA</td>';
                            }
                            $outputString.='<td>'.$item->ship_to_location.'</td>';

                        }
                        //has multiple POs, loop
                    }else{
                        $outputString.='<tr><td>'.$item->purchase_orders.'</td>';
                        $outputString.='<td>'.$item->shipment_id_arn.'</td>';
                        $outputString.='<td>'.$item->carrier_request_id.'</td>';
                        if($item->carrier){
                            //has carrier listed, output it
                            $outputString.='<td>'.$item->carrier.'</td>';
                        }else{
                            $outputString.='<td>NA</td>';
                        }
                        if($item->shipment_type){
                            //has carrier listed, output it
                            $outputString.='<td>'.$item->shipment_type.'</td>';
                        }else{
                            $outputString.='<td>NA</td>';
                        }
                        $outputString.='<td>'.$item->ship_to_location.'</td>';
                    }
                  //  echo $item->purchase_orders.'<br>';

                }
                $outputString.='</table></div>';
               // dd($outputString);
               // echo $outputString;




            });

                $data['response']=$outputString;
                return View::make('parseAmazonCsv-output', $data);
        }else{
            //validation fails
$data['response']="<br><span style='color:red;'>Please select a .csv file to parse.</span>";
            return View::make('parseAmazonCsv-input',$data);
        }

    }

}

function hasComma($str){
    if (strpos($str, ',') !== FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}