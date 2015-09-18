<?php

class ItemController extends \BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        return View::make('input');
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

            $tempitems = trim(Input::get('items'));
            Session::flash('items', $tempitems);
            $items = explode(PHP_EOL, $tempitems);
            $totalitems = count($items);
            $i = 1;
            $stringresponse = '';
            $optionResult = Input::get('optionsRadios');
            if ($optionResult == 'ordernumbers') {
                $stringresponse .= 'om_f.ob_oid in ';
            } elseif($optionResult=='shipmentnumbers') {
                $stringresponse .= 'om_f.shipment in ';
            }
            if ($totalitems == 1) {
                $stringresponse .= "('" . $items[0] . "')";
            } else {
                foreach ($items as $item) {
                    if ($i == 1) {
                        $stringresponse .= "('" . $item . "',";
                    } elseif ($i == $totalitems) {
                        $stringresponse .= "'" . $item . "')";
                    } else {
                        $stringresponse .= "'" . $item . "',";
                    }
                    $i++;
                }
            }
            return View::make('join-output')->with(array('response' => $stringresponse));

        }

    }

    /**
     * load Grabs the excel file items.xlsx and adds/update the cmmfs in the DB
     */
    public function load()
    {


        Excel::load(storage_path() . '\items.xlsx', function ($reader) {


// Loop through all sheets
            $reader->each(function ($sheet) {

                // Loop through all rows
                $sheet->each(function ($row) {


                    if ($row->cmmf != null) {
                        //row in cmmf input is not blank so add/edit item
                        $item = Item::where('cmmf', '=', $row->cmmf)->first();
                        if ($item == null) {
                            //item doesn't exist yet
                            $item = new Item();
                            $item->cmmf = $row->cmmf;
                            $item->case = $row->case_pack;
                            $item->weight = $row->weight_lb;
                            $item->size = $row->size;
                            $item->cartonsperpallet = $row->ctnspallet;
                            $item->save();
                            unset($item);
                        } else {
                            //cmmf already exists..just update it
                            $item->cmmf = $row->cmmf;
                            $item->case = $row->case_pack;
                            $item->weight = $row->weight_lb;
                            $item->size = $row->size;
                            $item->cartonsperpallet = $row->ctnspallet;
                            $item->save();
                            unset($item);
                        }

                    }

                });

            });
        });

        echo 'loading DB complete.<br><br>';
        echo link_to_route('input', 'Go back.');

    }

    /**
     * input() takes Input::all and creates a table with info
     * @return associative array $data to views
     */
    public function input()
    {
        $validator = Validator::make(Input::all(),
            array(
                'cmmf' => 'required',
                'quantity' => 'required'
            )
        );
        if ($validator->fails()) {
            return View::make('input')->with(array('response' => '<p style="color:red;">Please check your input and try again.</p>'));
        } else {

            $notFoundItems = array();
            $cmmf = trim(Input::get('cmmf'));
            //Stores the inputted cmmfs and quantities in Session variables incase user wants to use Back button to redo query
            Session::flash('cmmf', $cmmf);
            $quantity = trim(Input::get('quantity'));
            Session::flash('quantity', $quantity);

            //broken up by newline to be processes line by line
            $quantities = explode(PHP_EOL, $quantity);
            $cmmfs = explode(PHP_EOL, $cmmf);
            //$stats is var storing the table data for the summary of the shipment
            $stats = '<div class="CSSTableGenerator" >
                <table id="stats" ><tr>
                        <td>
                            Total Pieces
                        </td>
                        <td >
                            Total Cartons
                        </td>
                        <td>
                        Total Weight
                        </td>
                        <td>
                        Total Pallets
                        </td>
                    <td>
                    Total Spaces
                    </td>



                    </tr>';

            //if the amount of cmmfs supplied doesn't match the amount of quantities supplied then state error
            if (count($cmmfs) != count($quantities)) {

                return View::make('input', array('response' => '<p style="color:red;">ERROR: NUMBER OF CMMFS DO NOT MATCH THE NUMBER OF QUANTITIES SUPPLIED.</p>'));
            }


            $totalQuantity = null;
            $totalCartons = null;
            $totalWeight = null;
            $totalPallets = null;
            $totalSpaces = null;

            //$i tracks the index for each line items of both the inputted cmmf and quantity array
            $i = 0;
            $response = '<div class="CSSTableGenerator" >
                <table id="table" ><tr>
                        <td>
                            CMMF
                        </td>

                        <td >
                            Quantity
                        </td>
                        <td>Size</td>
                         <td>Case</td>
                        <td>
                        Total Cases
                        </td>
                          <td>
                        Weight
                        </td>
                        <td>
                        Total Weight
                        </td>
                        <td>Cartons per Pallet</td>
                        <td>
                    Total Pallets
                    </td>

                    <td>
                    Total Spaces
                    </td>



                    </tr>';
            //now checks each of the cmmfs (broken up by newline) supplied from input
            foreach ($cmmfs as $cmmf) {

                $item = Item::where('cmmf', '=', $cmmf)->first();
                //checks if cmmf
                if ($item == null) {
                    //item isn't found, so added to the $notFoundItems array to be displayed later and shows as NA for that row/item
                    array_push($notFoundItems, $cmmf);
                    //echo $cmmf . ' not in DB<br>';
                    $response .= '<tr>
                        <td>
                            ' . $cmmf . '
                        </td>
                        <td >
                            ' . $quantities[$i] . '
                        </td>
                         <td>NA</td>
                         <td>NA</td>
                        <td>
                        NA
                        </td>
                         <td>
                        NA
                        </td>
                         <td>
                        NA
                        </td>
                         <td>
                        NA
                        </td>
                        <td>
                        NA
                        </td>
                    <td>
                    NA
                    </td>


                    </tr>';

                } else {
                    $linequantity = $quantities[$i];
                    $varcmmf = $item->cmmf;


                    // add info to totals variables
                    $totalQuantity += $linequantity;
                    $totalCartons += ceil($item->getCartonCount($linequantity));
                    $totalWeight += $item->getWeight($linequantity);
                    $totalPallets += $item->getPalletCount($linequantity);
                    $totalSpaces += $item->getSpaceCount($linequantity);
                    //end of info to totals variables

                    $response .= '<tr>
                        <td>
                            ' . $varcmmf . '
                        </td>
                        <td >
                            ' . $linequantity . '
                        </td>
                        <td>' . $item->size . '</td>
                         <td>' . $item->case . '</td>
                        <td>
                       ' . ceil($item->getCartonCount($linequantity)) . '
                        </td>
                        <td>
                        ' . number_format($item->weight, 2, '.', '') . '
                        </td>
                        <td>
                       ' . number_format($item->getWeight($linequantity), 2, '.', '') . '
                        </td>
                        <td>
                        ' . $item->cartonsperpallet . '
                        </td>
                              <td>
                    ' . number_format($item->getPalletCount($linequantity), 2, '.', '') . '
                    </td>

                      <td>
                  ' . number_format($item->getSpaceCount($linequantity), 2, '.', '') . '
                    </td>


                    </tr>';

//                echo $item->getSpaceCount($quantities[$i]) . '<br>';
                }
                $i++;
            }


            $stats .= '<tr>
<td>' . $totalQuantity . '

</td>
<td>
' . $totalCartons . '
</td>
<td>
' . number_format($totalWeight, 2, '.', '') . '
</td>
<td>
' . number_format($totalPallets, 2, '.', '') . '
</td>
<td>
' . number_format($totalSpaces, 2, '.', '') . '
</td>


</tr></table>
            </div>
            ';
            $response .= '                </table>
            </div>
            ';
            $data['stats'] = $stats;
            $data['response'] = $response;
            $data['missingitems'] = array_unique($notFoundItems);
            return View::make('output-table')->with($data);
        }


    }


}
