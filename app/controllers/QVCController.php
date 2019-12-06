<?php

class QVCController extends \BaseController
{

    function __construct()
    {
        //   include(app_path().'/includes/fpdf_tpl.php');

//        include(app_path().'/includes/tcpdi.php');
        //    include(app_path().'/includes/tcpdf_filters.php');
        // include(app_path().'/includes/tcpdi_parser.php');

    }

    private function prepareArray($inputitems)
    {
        $tempItems = trim($inputitems);
        $items = explode(PHP_EOL, $tempItems);

        $finalArray = array();
        foreach ($items as $item) {
            if (trim($item) != '') {
                array_push($finalArray, trim($item));
            }

        }
        return $finalArray;


    }


    public function Postcheckforground()
    {
        $validator = Validator::make(Input::all(),
            array(
                'pos' => 'required'
            )
        );


        if (!$validator->fails()) {
            $nonGroundPOS = array();
            $notInDBPOS = array();

            $inputArray = $this->prepareArray(Input::get('pos'));
            foreach ($inputArray as $tempItem) {
                $tempPO = QVCPackingList::where('po', '=', $tempItem)->first();
                if ($tempPO == null) {
                    array_push($notInDBPOS, $tempItem);
                    //not in db
                } else {
                    if ($tempPO->shipterms != 'Ground') {
                        array_push($nonGroundPOS, $tempItem);
                    }

                }

            }
            $queryPOString = $this->joinQVCParsePO($nonGroundPOS);
            $data['nonGroundPos'] = $nonGroundPOS;
            $data['notInDBPos'] = $notInDBPOS;
            $data['queryPOString'] = $queryPOString;
//dd(empty($nonGroundPOS));
            //  dd($nonGroundPOS);
            if ((empty($nonGroundPOS) && empty($notInDBPOS))) {
                $data['response'] = 'All of the provided POs are in the DB and are going Ground.';
            }
            return View::make('QVC-check-if-ground-output', $data);


        } else {

            return View::make('QVC-check-if-ground-output')->with(array('response' => '<p style="color:red;">Please provide a list of POs</p>'));

        }

    }

    function split_pdf($filePath, $fileName, $individualFileName, $arrayOfPos, $end_directory = false)
    {
        $new_path = storage_path() . '/QVCpos';


        if (!is_dir($new_path)) {
            // Will make directories under end directory that don't exist
            // Provided that end directory exists and has the right permissions
            mkdir($new_path, 0777, true);
        }

        $pdf = new FPDI();
        $pagecount = $pdf->setSourceFile($filePath); // How many pages?
//dd($pagecount);
        // Split each page into a new PDF
        for ($i = 1; $i <= $pagecount; $i++) {
            $tempQVCPackingList = QVCPackingList::where('po', '=', $arrayOfPos[$i - 1])->first();

            if ($tempQVCPackingList == null) {
                $new_pdf = new FPDI();
                $new_pdf->AddPage();
                $new_pdf->setSourceFile($filePath);
                $newQVCPackingList = new QVCPackingList();
                $newQVCPackingList->po = trim($arrayOfPos[$i - 1]);
                $new_pdf->useTemplate($new_pdf->importPage($i));

                try {
                    $new_filename = storage_path() . '/QVCpos/' . $arrayOfPos[$i - 1] . '.pdf';

                    $newQVCPackingList->pathToFile = $new_filename;
                    $new_pdf->Output($new_filename, "F");
                    $newQVCPackingList->save();

                } catch (Exception $e) {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }
            }
        }
    }

    function split_multi_pdf($arrayOfFiles)
    {

        $new_path = storage_path() . '/QVCpos';


        if (!is_dir($new_path)) {
            // Will make directories under end directory that don't exist
            // Provided that end directory exists and has the right permissions
            mkdir($new_path, 0777, true);
        }
        foreach ($arrayOfFiles as $file) {
            $tempArrayOfPos = array();
            //    $infoOfFile=$this->getArrayOfPOs($file);
            $tempArrayOfPos = $this->getArrayOfPOs($file);
            $pdf = new FPDI();
            $pagecount = $pdf->setSourceFile($file); // How many pages?
            // $pagecount=$infoOfFile['poCount'];
            for ($i = 1; $i <= $pagecount; $i++) {
                $singleItem = $tempArrayOfPos[$i - 1];
                //   dd($singleItem);
                $tempQVCPackingList = QVCPackingList::where('po', '=', $singleItem['PO'])->first();

                if ($tempQVCPackingList == null) {
                    $new_pdf = new FPDI();
                    $new_pdf->AddPage();
                    $new_pdf->setSourceFile($file);
                    $newQVCPackingList = new QVCPackingList();
                    $newQVCPackingList->po = trim($singleItem['PO']);
                    $newQVCPackingList->shipterms = trim($singleItem['shipterms']);
                    $new_pdf->useTemplate($new_pdf->importPage($i));

                    try {
                        $new_filename = storage_path() . '/QVCpos/' . $singleItem['PO'] . '.pdf';
                        $newQVCPackingList->pathToFile = $new_filename;
                        $new_pdf->Output($new_filename, "F");
                        $newQVCPackingList->save();

                    } catch (Exception $e) {
                        echo 'Caught exception: ', $e->getMessage(), "\n";
                    }
                }
            }

        }


    }

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

    public function index()
    {


    }

    function getArrayOfPOs($file)
    {
        $returnArray = array();
        //    $returnArray['items']=array();
        $parser = new \Smalot\PdfParser\Parser();

        $pdf = $parser->parseFile($file);
        $pages = $pdf->getPages();
        // dd($pages);
//dd($pages);
        //     $poCount=0;
//dd($pages);

        foreach ($pages as $page) {

            if ($page!=null) {
                //    $poCount++;
                $text = nl2br($page->getText());

                $tempPDF = explode('<br />', $text);


                $getPO = $tempPDF[14];
                $getPO=trim($getPO);
                $data['PO'] = substr($getPO,-10);
                $shipTerms = $this->checkShipMethod($text);
                if($shipTerms==false){
                    dd('PO '.$data['PO'].' has a unrecognized ship method. Please show this to Jason so it can be added');

                }
                $data['shipterms'] = $shipTerms;

//                if ($isGround) {
//                    $data['shipterms'] = "Ground";
//                } else {
//                    $data['shipterms'] = "Not Ground";
//                }
                //    $PO = trim($getPO[1]);
                array_push($returnArray, $data);
            }

        }
//dd($returnArray);
        // $returnArray['poCount']=$poCount;

        // dd($returnArray);
        return $returnArray;

    }

//    function checkIfGround($haystack)
//    {
//
//        if (strpos($haystack, 'Continental US - Standard Ground') !== false) {
//            return true;
//        } elseif (strpos($haystack, 'UPS Ground') !== false) {
//
//            return true;
//        } elseif (strpos($haystack, 'Alaska/Hawaii & APO/FPO - Standard Ground') !== false) {
//
//            return true;
//        }
//        else {
//            return false;
//        }
    function checkShipMethod($haystack)
    {
        return 'Ground'; // qvc all ground so no check needed.
        // if (strpos($haystack, 'Continental US - Standard Ground') !== false) {
        if (strpos($haystack, 'UPS G ro und') !== false) {

            return 'Ground';
        } elseif (strpos($haystack, 'UPS Ground') !== false) {

            return 'Ground';
        } elseif (strpos($haystack, 'FedE x 2 D ay') !== false) {

            return 'Ground';
        }
        elseif (strpos($haystack, 'FedE x S ta ndard  O vern ig ht') !== false) {

            return 'Ground';
        }
        elseif (strpos($haystack, 'FedE x H om e D eliv ery') !== false) {

            return 'Ground';
        } elseif (strpos($haystack, 'Alaska/Hawaii & APO/FPO - Standard Ground') !== false) {

            return 'Ground';
        }
        elseif (strpos($haystack, 'Generic  S econd D ay') !== false) {

            return '2D';
        }
        elseif (strpos($haystack, 'UPS 2nd Day Air') !== false) {

            return '2D';
        }
        elseif (strpos($haystack, 'Overnight') !== false) {

            return 'ND';
        }
        elseif (strpos($haystack, 'Generic  N ext D ay') !== false) {

            return 'ND';
        }
        elseif (strpos($haystack, 'UPS 3 Day Select') !== false) {

            return '3D';
        }
        else {
            return false;
        }
//         $result= strpos($haystack, 'Continental US - Standard Ground') !== false || strpos($haystack, 'UPS Ground') !== false;

// return $result;
    }

    public function parseGetPDF()
    {
        return View::make('parsepdfQVC-importpdf-input');

    }

    public function parsePostPDF()
    {

//        $validator = Validator::make(Input::all(),
//            array(
//                'QVCPackingList' => 'required'
//            )
//        );
//
//
//        if (!$validator->fails()) {
        if(Input::file('QVCPackingList')[0]!=NULL){
            //validation passes
            $files = Input::file('QVCPackingList');
            $this->split_multi_pdf($files);
//            $file = Input::file('QVCPackingList');
//            $name = Input::file('QVCPackingList')->getClientOriginalName();
//            $file_name = pathinfo($name, PATHINFO_FILENAME); // file
//            $parser = new \Smalot\PdfParser\Parser();
//
//            $pdf = $parser->parseFile($file);
//            $pages = $pdf->getPages();
//            $arrayOfPos = array();
//
//
//            foreach ($pages as $page) {
//                $text = nl2br($page->getText());
//
//                $tempPDF = explode('<br />', $text);
//
//
//                $getPO = explode(':', $tempPDF[10]);
//                $PO = trim($getPO[1]);
//                array_push($arrayOfPos, $PO);
//
//            }

            //    $pdf = new FPDI();
            //   $pagecount = $pdf->setSourceFile($file);
//          print_r($pagecount);
//            print_r($arrayOfPos);
            // dd(storage_path());


            //  $this->split_pdf($file, $name, $file_name, $arrayOfPos, public_path());
            // How many pages?
            //  $totalOfPos = count($arrayOfPos);
            //  $queryString = $this->joinQVCParsePO($arrayOfPos);
            //    $data['POs'] = $arrayOfPos;
            //   $returnPOString='';
//            foreach($arrayOfPos as $returnPO){
//                $returnPOString.=$returnPO.'<br>';
//            }
//            $data['POs']=$returnPOString;
//            $data['totalOfPOs'] = $totalOfPos;
//            $data['queryString'] = $queryString;
            return View::make('parsepdfQVC-importpdf-output');
        } else {
//validation fails
            return View::make('parsepdfQVC-importpdf-input')->with(array('response' => '<p style="color:red;">Please select a packing list pdf to parse.</p>'));

        }


    }

    public function retrieveGetPDF()
    {

        return View::make('parsepdfQVC-exportpdf-input');
    }

    public function retrievePostPDF()
    {

        $validator = Validator::make(Input::all(),
            array(
                'QVCPackingList' => 'required'
            )
        );


        if (!$validator->fails()) {
            include(app_path() . '/includes/PDFMerger.php');
            $notFoundPOs = array();
            $nonGroundPOs = array();
            $groundPOs=array();
            $overnightPos=array();
            $secondDayPos=array();
            $thirdDayPos=array();
            $groundQVCPackingListPathArray = array();
            $overnightQVCPackingListPathArray = array();
            $secondDayQVCPackingListPathArray = array();
            $thirdDayQVCPackingListPathArray = array();

            //  $QVCPackingListPOsArray = array();
            $QVCPackingListPathArray = array();
            //   $QVCPackingListPOs = trim(Input::get('QVCPackingList'));
            Session::flash('QVCPackingList', Input::get('QVCPackingList'));
            //  $QVCPackingListPOsArray = explode(PHP_EOL, $QVCPackingListPOs);
            $QVCPackingListPOsArray=$this->prepareArray(trim(Input::get('QVCPackingList')));
            foreach ($QVCPackingListPOsArray as $QVCPackingListPO) {
                // $tempQVCPackingList = QVCPackingList::where('po', '=', $QVCPackingListPO)->first();
                //for QVC, have to truncate the first ten digits
                $tempQVCPackingList = QVCPackingList::where('po', '=', substr($QVCPackingListPO,0,10))->first();
                if ($tempQVCPackingList == null) {
                    //do not have packing list yet
                    array_push($notFoundPOs, $QVCPackingListPO);
                } else {
                    //  array_push($QVCPackingListPathArray, $tempQVCPackingList->pathToFile);
                    // dd($tempQVCPackingList->shipterms=='Ground');
//                    if ($tempQVCPackingList->shipterms != 'Ground') {
//                        array_push($nonGroundPOs, $QVCPackingListPO);
//                    }

                    switch ($tempQVCPackingList->shipterms) {
                        case 'Ground':
                            array_push($groundQVCPackingListPathArray,$tempQVCPackingList->pathToFile);
                            array_push($groundPOs,$QVCPackingListPO);
                            break;
                        case 'ND':
                            array_push($overnightQVCPackingListPathArray,$tempQVCPackingList->pathToFile);
                            array_push($overnightPos,$QVCPackingListPO);
                            break;
                        case '2D':
                            array_push($secondDayQVCPackingListPathArray,$tempQVCPackingList->pathToFile);
                            array_push($secondDayPos,$QVCPackingListPO);
                            break;
                        case '3D':
                            array_push($thirdDayQVCPackingListPathArray,$tempQVCPackingList->pathToFile);
                            array_push($thirdDayPos,$QVCPackingListPO);
                            break;
                    }



                }

            }
            if (count($notFoundPOs) > 0) {
                $notFoundPOsReturnString = '';
                foreach ($notFoundPOs as $notFoundPO) {
                    $notFoundPOsReturnString .= $notFoundPO . '<br>';
                }
                return View::make('parsepdfQVC-exportpdf-output')->with(array('response' => '<p style="color:red;">The below POs are missing:</p><br>' . $notFoundPOsReturnString));


            } else {
//                $groundPOs=array();
//                $overnightPos=array();
//                $secondDayPos=array();
//                $thirdDayPos=array();
//                $groundQVCPackingListPathArray = array();
//                $overnightQVCPackingListPathArray = array();
//                $secondDayQVCPackingListPathArray = array();
//                $thirdDayQVCPackingListPathArray = array();
                //different types of shipment variables just listed above to remember
                //return a download file..we have all Packing lists

                //if only type of ship method exists for the request group of POs ... only return the path to the file and not the queries

                if(empty($overnightQVCPackingListPathArray)&&empty($secondDayQVCPackingListPathArray)&&empty($thirdDayQVCPackingListPathArray)){
                    //beginning of making a group of packing list, need to now make for seperate types
                    $pdf = new PDFMerger;
                    foreach ($groundQVCPackingListPathArray as $QVCPackingListPath) {
                        $pdf->addPDF($QVCPackingListPath);
                    }
                    $tempoutputpath = 'output' . '-' . time() . '.pdf';
                    $outputpath = public_path() . '/QVCpos/' . $tempoutputpath;
                    $pdf->merge('file', $outputpath);
                    $outputpath = 'QVCpos/' . $tempoutputpath;
                    //end of making a group of QVCPackingList

                    $data['response'] = $this->createDownloadLink($outputpath,'Click here to download the generated packing lists (All Ground)');
                    // dd($nonGroundPOs);
                    //    $data['outputpath'] = $outputpath;



                    return View::make('parsepdfQVC-exportpdf-output', $data);

                }else{
                    //response to build
                    $response='';

                    //other types of ship methods exist so return associated queries with them

                    //begin ground
                    if(empty($groundQVCPackingListPathArray)==false){
                        $pdf = new PDFMerger;
                        foreach ($groundQVCPackingListPathArray as $QVCPackingListPath) {
                            $pdf->addPDF($QVCPackingListPath);
                        }
                        $tempoutputpath = 'output' . '-' . time() .'ground'. '.pdf';
                        $outputpath = public_path() . '/QVCpos/' . $tempoutputpath;
                        $pdf->merge('file', $outputpath);
                        $outputpath = 'QVCpos/' . $tempoutputpath;
                        $response.='Query for Ground POs: <br><br>';
                        $response.=$this->joinPurchaseOrders($groundPOs).'<br><br>';
                        $response.=$this->createDownloadLink($outputpath,'Click here to download the Ground packing lists');


                    }


                    //end ground


                    //begin overnight
                    if(empty($overnightQVCPackingListPathArray)==false){
                        $pdf = new PDFMerger;
                        foreach ($overnightQVCPackingListPathArray as $QVCPackingListPath) {
                            $pdf->addPDF($QVCPackingListPath);
                        }
                        $tempoutputpath = 'output' . '-' . time().'on' . '.pdf';
                        $outputpath = public_path() . '/QVCpos/' . $tempoutputpath;
                        $pdf->merge('file', $outputpath);
                        $outputpath = 'QVCpos/' . $tempoutputpath;
                        $response.='Query for Overnight POs: <br><br>';
                        $response.=$this->joinPurchaseOrders($overnightPos).'<br><br>';
                        $response.=$this->createDownloadLink($outputpath,'Click here to download the Overnight packing lists');


                    }
                    //end overnight


                    //begin second day
                    if(empty($secondDayQVCPackingListPathArray)==false){
                        $pdf = new PDFMerger;
                        foreach ($secondDayQVCPackingListPathArray as $QVCPackingListPath) {
                            $pdf->addPDF($QVCPackingListPath);
                        }
                        $tempoutputpath = 'output' . '-' . time() .'2nd'. '.pdf';
                        $outputpath = public_path() . '/QVCpos/' . $tempoutputpath;
                        $pdf->merge('file', $outputpath);
                        $outputpath = 'QVCpos/' . $tempoutputpath;
                        $response.='Query for 2nd Day POs: <br><br>';
                        $response.=$this->joinPurchaseOrders($secondDayPos).'<br><br>';
                        $response.=$this->createDownloadLink($outputpath,'Click here to download the 2nd Day packing lists');

                    }

                    //end second day


                    //begin third day
                    if(empty($thirdDayQVCPackingListPathArray)==false){
                        $pdf = new PDFMerger;
                        foreach ($thirdDayQVCPackingListPathArray as $QVCPackingListPath) {
                            $pdf->addPDF($QVCPackingListPath);
                        }
                        $tempoutputpath = 'output' . '-' . time() .'3rd'. '.pdf';
                        $outputpath = public_path() . '/QVCpos/' . $tempoutputpath;
                        $pdf->merge('file', $outputpath);
                        $outputpath = 'QVCpos/' . $tempoutputpath;
                        $response.='Query for 3rd Day POs: <br><br>';
                        $response.=$this->joinPurchaseOrders($thirdDayPos).'<br><br>';
                        $response.=$this->createDownloadLink($outputpath,'Click here to download the 3rd Day packing lists');

                    }

                    //end third day
                    $data['response']=$response;
                    return View::make('parsepdfQVC-exportpdf-output', $data);
                }



                //beginning of making a group of packing list, need to now make for seperate types
//                $pdf = new PDFMerger;
//                foreach ($QVCPackingListPathArray as $QVCPackingListPath) {
//                    $pdf->addPDF($QVCPackingListPath);
//                }
//                $tempoutputpath = 'output' . '-' . time() . '.pdf';
//                $outputpath = public_path() . '/QVCpos/' . $tempoutputpath;
//                $pdf->merge('file', $outputpath);
//                $outputpath = 'QVCpos/' . $tempoutputpath;
//                //end of making a group of QVCPackingList
//
//                $data['nonGroundPos'] = $nonGroundPOs;
                // dd($nonGroundPOs);
                //    $data['outputpath'] = $outputpath;






// REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options

            }

        } else {

            return View::make('parsepdfQVC-exportpdf-input')->with(array('response' => '<p style="color:red;">Please add a list of POs below.</p>'));

        }
    }

    function createDownloadLink($path,$message){
        return '<a href="'.$path.'" target="_blank">'.$message.'</a><br><br><hr>';
    }

    function deleteDBForm()
    {
        return View::make('deleteQVCDBForm');

    }

    function deleteDBSubmission()
    {
        $result = Input::get('delete');
        if ($result != 'Y-E-S') {
            $data['response'] = '<span style="color:red">DB reset has failed. Y-E-S was not entered.</span>';
            return View::make('deleteQVCDBForm', $data);
        } else {

            QVCPackingList::truncate();
            $this->rrmdir(storage_path() . '/QVCpos');
            $this->rrmdir(public_path() . '/QVCpos');

            $data['response'] = '<span style="color:red">All items in the QVC DB have been cleared.</span>';
            return View::make('deleteQVCDBForm', $data);
        }

    }

    private function joinQVCParsePO($ordersArray)
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

    function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        $this->rrmdir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            // rmdir($dir);
        }
    }

}
