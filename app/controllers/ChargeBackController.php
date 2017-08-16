<?php

class ChargeBackController extends \BaseController
{


    function getChargeBack()
    {
        return View::make('parseChargeBackCsv-input');

    }

    function postChargeBack()
    {

        $outputString = '<div class="CSSTableGenerator"><table id="table"><tr><td>ShortStore&PO</td><td>LongStore&PO</td><td>PO</td><td>Created</td><td>Req.Ship.Date</td><td>Del.Date</td><td>Del.Instructions</td><td>Last Ship Day</td><td>Days to complete.</td><td># of Ships</td><td>Summary</td></tr>';

        if (Input::file('chargeBackSpreadSheet') != NULL) {
            //validation passes

            $file = Input::file('chargeBackSpreadSheet');
            Excel::load($file, function ($reader) use (&$outputString) {
                $POStoreArray = array();
                // $Shipments = array();
                $items = $reader->all();


                foreach ($items as $item) {

                    if (isPOStoreInArray($POStoreArray, returnLongStorePOResult($item))) {
                        $POStoreindex = FindIndexOfPOStoreInArray($POStoreArray, returnLongStorePOResult($item));


                        if (checkIfShipmentInPOStore($POStoreArray[$POStoreindex], $item->shipment)) {
                            // nothing to add since the same SHP is in the POStore SHP's array
                        } else {

                            //shipment doesnt exist in the POStore SHP's array, add it
                            $newShipment = new Shipment();
                            $newShipment->creationDate = $item->created_on;
                            $newShipment->pickDate = $item->pick_date;
                            $newShipment->shpNumber = $item->shipment;
                            $newShipment->SCAC = $item->scac;
                            $newShipment->shipDate = $item->acgi_date;
                            $newShipment->computeDaysInbetweenCreationAndShip();
                            $newShipment->computeDaysInbetweenPickAndShip();
                            array_push($POStoreArray[$POStoreindex]->shipments, $newShipment);

                        }
                    } else {

                        //po store is not in array, add PO Store and shipment
                        //Create POStore object
                        $newPOwithStore = new POwithStore();
                        $newPOwithStore->longStorePO = returnLongStorePOResult($item);
                        $newPOwithStore->shortStorePO = returnShortStorePOResult($item);
                        $newPOwithStore->creationDate = $item->created_on;
                        $newPOwithStore->cancel_date = $item->cancel_date;
                        $newPOwithStore->requested_ship_date = $item->requested_ship_date;
                        $newPOwithStore->PO = $item->purchase_order_no;
                        $newPOwithStore->deliveryDate = $item->delivery_date;
                        $newPOwithStore->deliveryInstructions = $item->delivery_date_instruction;
                        //create shipment
                        $newShipment = new Shipment();
                        $newShipment->pickDate = $item->pick_date;
                        $newShipment->creationDate = $item->created_on;
                        $newShipment->shpNumber = $item->shipment;
                        $newShipment->SCAC = $item->scac;
                        $newShipment->shipDate = $item->acgi_date;
                        $newShipment->computeDaysInbetweenCreationAndShip();
                        $newShipment->computeDaysInbetweenPickAndShip();
                        array_push($newPOwithStore->shipments, $newShipment);
                        array_push($POStoreArray, $newPOwithStore);
                    }

                }
                // end off main for loop
                //finished processesing the Excel file. Now run methods on POStore object and then create Output table string
                foreach ($POStoreArray as $individualPOStore) {
                    $individualPOStore->computeLastShipDate();
                    $individualPOStore->returnTotalDaysInbetweenToComplete();
                    $individualPOStore->reorderShipments();
                    $outputString .= $individualPOStore->summarizePOStore();
                }
                $outputString .= '</table></div>';
            });
            $data['response'] = $outputString;
            return View::make('parseChargeBackCsv-output', $data);
        } else {
            // no file supplied
            $data['response'] = "<br><span style='color:red;'>Please select a spreadsheet file to parse.</span>";
            return View::make('parseChargeBackCsv-input', $data);

        }

    }

}
/*
 * Checks if POStore already exist in the POStore array
 */
function isPOStoreInArray($poStore, $LongStorePOResult)
{
    if (empty($poStore)) {
        return false;
    }
    foreach ($poStore as $item) {
        if ($item->longStorePO == $LongStorePOResult) {
            return true;
        }
    }
    return false;

}
/*
 * If the POStore does exist in Array, return the index of it
 */
function FindIndexOfPOStoreInArray($poStore, $LongStorePOResult)
{
    $i = 0;
    foreach ($poStore as $item) {
        if ($item->longStorePO == $LongStorePOResult) {
            return $i;
        }
        $i++;
    }


}

/*
 * Checks the Shipment array in POStore object to see if a shipment existed already
 */

function checkIfShipmentInPOStore($POStoreEntry, $Shipment)
{
    foreach ($POStoreEntry->shipments as $CurrentShipment) {
        if ($CurrentShipment->shpNumber == $Shipment) {
            return true;
        }
    }
    return false;

}

/*
 * Returns a string of the store number only
 */
function returnOnlyStoreNumbers($storeString)
{
    return intval(preg_replace('/[^0-9]+/', '', $storeString), 10);
}

/*
 * Returns a string concatenated with the whole store name with the PO
 */
function returnLongStorePOResult($item)
{
    return $item->name_1 . "-" . $item->purchase_order_no;
}

/*
 * Returns a string concatenated with the store number only with the PO
 */
function returnShortStorePOResult($item)
{
    return returnOnlyStoreNumbers($item->name_1) . "-" . $item->purchase_order_no;
}

function returnDaysInbetween($startDate, $endDate)
{
    $end = Carbon\Carbon::parse($endDate);
    return $end->diffInDays($startDate);

}

/*  Example for DATA returned from Excel Sheet
 *
 *   'name_1' => string 'RITE AID WOODLAND DC #81' (length=24)
      'purchase_order_no' => string '6933167' (length=7)
      'sales_doc' => string '1841973618' (length=10)
      'delivery' => string '2513576222' (length=10)
      'shipment' => string 'SHP3663674' (length=10)
      'addittext_1' => string '497802209X' (length=10)
      'addittext_2' => null
      'external_id_1' => null
      'external_id_2' => string '00324063000819123' (length=17)
      'scac' => string 'RDWY' (length=4)
      'created_on' =>
        object(Carbon\Carbon)[574]
          public 'date' => string '2016-11-14 00:00:00.000000' (length=26)
          public 'timezone_type' => int 3
          public 'timezone' => string 'UTC' (length=3)
      'pick_date' =>
        object(Carbon\Carbon)[590]
          public 'date' => string '2016-11-15 00:00:00.000000' (length=26)
          public 'timezone_type' => int 3
          public 'timezone' => string 'UTC' (length=3)
      'acgi_date' =>
        object(Carbon\Carbon)[589]
          public 'date' => string '2016-11-21 00:00:00.000000' (length=26)
          public 'timezone_type' => int 3
          public 'timezone' => string 'UTC' (length=3)
      'requested_ship_date' =>
        object(Carbon\Carbon)[588]
          public 'date' => string '2016-11-15 00:00:00.000000' (length=26)
          public 'timezone_type' => int 3
          public 'timezone' => string 'UTC' (length=3)
      'delivery_date' =>
        object(Carbon\Carbon)[577]
          public 'date' => string '2016-11-28 00:00:00.000000' (length=26)
          public 'timezone_type' => int 3
          public 'timezone' => string 'UTC' (length=3)
      'delivery_date_instruction' => string 'MUST ARRIVE BY 11/28/2016.' (length=26)
      'cancel_date' =>
        object(Carbon\Carbon)[586]
          public 'date' => string '2016-11-15 00:00:00.000000' (length=26)
          public 'timezone_type' => int 3
          public 'timezone' => string 'UTC' (length=3)
 *
 *
 */

class Shipment
{
    public $shpNumber;
    public $creationDate;
    public $pickDate;
    public $shipDate;
    public $daysInbetweenPickAndShip;
    public $daysInbetweenCreationAndShip;
    public $SCAC;

    function computeDaysInbetweenPickAndShip()
    {
        $end = Carbon\Carbon::parse($this->shipDate);
        $this->daysInbetweenPickAndShip = $end->diffInDays($this->pickDate);

    }

    function computeDaysInbetweenCreationAndShip()
    {
        $end = Carbon\Carbon::parse($this->shipDate);
        $this->daysInbetweenCreationAndShip = $end->diffInDays($this->creationDate);

    }

    function summarizeShipment()
    {

        return $this->shpNumber . " (" . $this->SCAC . ") " . "[Pick:" . $this->pickDate->format('m-d-Y') . ", Ship:" . $this->shipDate->format('m-d-Y') . "][" . $this->daysInbetweenCreationAndShip . "/" . $this->daysInbetweenPickAndShip . "]";
    }

}

class POwithStore
{
    public $longStorePO;
    public $shortStorePO;
    public $shipments = array();
    public $PO;
    public $creationDate;
    public $lastShipDate;
    public $requested_ship_date;
    public $cancel_date;
    public $totalDaysToShipComplete;
    public $deliveryDate;
    public $deliveryInstructions;

    /*
 Compares "SHP#s" to re-order from oldest to newest
 */
    function cmp($a, $b)
    {
        return strcmp($a->shpNumber, $b->shpNumber);
    }

    /*
    Function reorders SHPs from oldest to newest
    */
    function reorderShipments()
    {

        usort($this->shipments, array($this, "cmp"));

    }

    function computeLastShipDate()
    {
        $index = 0;
        foreach ($this->shipments as $shipment) {
            if ($index == 0) {
                $this->lastShipDate = $shipment->shipDate;
                $index++;
            } else {

                if ($this->lastShipDate < $shipment->shipDate) {
                    $this->lastShipDate = $shipment->shipDate;
                }
                $index++;
            }
        }

    }

    function shipmentCount()
    {
        return count($this->shipments);
    }

    function summarizeShipments()
    {
        $output = "";
        if (count($this->shipments) == 1) {
            $output .= $this->shipments[0]->summarizeShipment();
            return $output;
        }
        $i = 0;
        foreach ($this->shipments as $shipment) {
            $i++;
            if ($i != count($this->shipments)) {
                $output .= $shipment->summarizeShipment() . " ||| ";
            } else {
                $output .= $shipment->summarizeShipment();
            }

        }
        return $output;

    }

    function getDelDate()
    {
        if ($this->deliveryDate != null) {
            return $this->deliveryDate->format('m-d-Y');
        } else {
            return "";
        }
    }


    function getReqShipDate()
    {
        if ($this->requested_ship_date != null) {
            return $this->requested_ship_date->format('m-d-Y');
        } else {
            return "";
        }
    }


    function getCreationDate()
    {
        if ($this->creationDate != null) {
            return $this->creationDate->format('m-d-Y');
        } else {
            return "";
        }
    }

    function summarizePOStore()
    {

        $output = "<tr>";
        $output .= "<td>" . $this->shortStorePO . "</td><td>" . $this->longStorePO . "</td><td>" . $this->PO . "</td><td>" . $this->getCreationDate() . "</td><td>" . $this->getReqShipDate() . "</td><td>" . $this->getDelDate() . "</td><td>" . $this->deliveryInstructions . "</td><td>" . $this->lastShipDate->format('m-d-Y') . "</td><td>" . $this->totalDaysToShipComplete . "</td><td>" . $this->shipmentCount() . "</td><td>" . $this->summarizeShipments() . "</td>";

        $output .= "</tr>";
        return $output;

    }

    function returnTotalDaysInbetweenToComplete()
    {
        $end = Carbon\Carbon::parse($this->lastShipDate);
        $this->totalDaysToShipComplete = $end->diffInDays($this->creationDate);

    }


}