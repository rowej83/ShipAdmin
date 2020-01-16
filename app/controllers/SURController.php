<?php

class SURController extends \BaseController
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
                $tempPO = SURPackingList::where('po', '=', $tempItem)->first();
                if ($tempPO == null) {
                    array_push($notInDBPOS, $tempItem);
                    //not in db
                } else {
                    if ($tempPO->shipterms != 'Ground') {
                        array_push($nonGroundPOS, $tempItem);
                    }

                }

            }
            $queryPOString = $this->joinSURParsePO($nonGroundPOS);
            $data['nonGroundPos'] = $nonGroundPOS;
            $data['notInDBPos'] = $notInDBPOS;
            $data['queryPOString'] = $queryPOString;
//dd(empty($nonGroundPOS));
            //  dd($nonGroundPOS);
            if ((empty($nonGroundPOS) && empty($notInDBPOS))) {
                $data['response'] = 'All of the provided POs are in the DB and are going Ground.';
            }
            return View::make('SUR-check-if-ground-output', $data);


        } else {

            return View::make('SUR-check-if-ground-output')->with(array('response' => '<p style="color:red;">Please provide a list of POs</p>'));

        }

    }

    function split_pdf($filePath, $fileName, $individualFileName, $arrayOfPos, $end_directory = false)
    {
        $new_path = storage_path() . '/SURpos';


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
            $tempSURPackingList = SURPackingList::where('po', '=', $arrayOfPos[$i - 1])->first();

            if ($tempSURPackingList == null) {
                $new_pdf = new FPDI();
                $new_pdf->AddPage();
                $new_pdf->setSourceFile($filePath);
                $newSURPackingList = new SURPackingList();
                $newSURPackingList->po = trim($arrayOfPos[$i - 1]);
                $new_pdf->useTemplate($new_pdf->importPage($i));

                try {
                    $new_filename = storage_path() . '/SURpos/' . $arrayOfPos[$i - 1] . '.pdf';

                    $newSURPackingList->pathToFile = $new_filename;
                    $new_pdf->Output($new_filename, "F");
                    $newSURPackingList->save();

                } catch (Exception $e) {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }
            }
        }
    }

    function split_multi_pdf($arrayOfFiles)
    {

        $new_path = storage_path() . '/SURpos';


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
                $tempSURPackingList = SURPackingList::where('po', '=', $singleItem['PO'])->first();

                if ($tempSURPackingList == null) {
                    $new_pdf = new FPDI();
                    $new_pdf->AddPage('L'); // sur la table needs landscape
                    $new_pdf->setSourceFile($file);
                    $newSURPackingList = new SURPackingList();
                    $newSURPackingList->po = trim($singleItem['PO']);
                    $newSURPackingList->shipterms = trim($singleItem['shipterms']);
                    $new_pdf->useTemplate($new_pdf->importPage($i));

                    try {
                        $new_filename = storage_path() . '/SURpos/' . $singleItem['PO'] . '.pdf';
                        $newSURPackingList->pathToFile = $new_filename;
                        $new_pdf->Output($new_filename, "F");
                        $newSURPackingList->save();

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


//                $getPO = $tempPDF[14];
//                $getPO=trim($getPO);

                foreach ($tempPDF as $line){
                    if(str_contains($line,'BEST WAY - STANDARD')){
                       $thePO=trim(substr($line,46,5)); // sur la ta
                        break;
                    }
                }
                $data['PO'] = $thePO;
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
        return 'Ground'; // SUR all ground so no check needed.
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
        return View::make('parsepdfSUR-importpdf-input');

    }

    public function parsePostPDF()
    {

//        $validator = Validator::make(Input::all(),
//            array(
//                'SURPackingList' => 'required'
//            )
//        );
//
//
//        if (!$validator->fails()) {
        if(Input::file('SURPackingList')[0]!=NULL){
            //validation passes
            $files = Input::file('SURPackingList');
            $this->split_multi_pdf($files);
//            $file = Input::file('SURPackingList');
//            $name = Input::file('SURPackingList')->getClientOriginalName();
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
            //  $queryString = $this->joinSURParsePO($arrayOfPos);
            //    $data['POs'] = $arrayOfPos;
            //   $returnPOString='';
//            foreach($arrayOfPos as $returnPO){
//                $returnPOString.=$returnPO.'<br>';
//            }
//            $data['POs']=$returnPOString;
//            $data['totalOfPOs'] = $totalOfPos;
//            $data['queryString'] = $queryString;
            return View::make('parsepdfSUR-importpdf-output');
        } else {
//validation fails
            return View::make('parsepdfSUR-importpdf-input')->with(array('response' => '<p style="color:red;">Please select a packing list pdf to parse.</p>'));

        }


    }

    public function retrieveGetPDF()
    {

        return View::make('parsepdfSUR-exportpdf-input');
    }

    public function retrievePostPDF()
    {

        $validator = Validator::make(Input::all(),
            array(
                'SURPackingList' => 'required'
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
            $groundSURPackingListPathArray = array();
            $overnightSURPackingListPathArray = array();
            $secondDaySURPackingListPathArray = array();
            $thirdDaySURPackingListPathArray = array();

            //  $SURPackingListPOsArray = array();
            $SURPackingListPathArray = array();
            //   $SURPackingListPOs = trim(Input::get('SURPackingList'));
            Session::flash('SURPackingList', Input::get('SURPackingList'));
            //  $SURPackingListPOsArray = explode(PHP_EOL, $SURPackingListPOs);
            $SURPackingListPOsArray=$this->prepareArray(trim(Input::get('SURPackingList')));
            foreach ($SURPackingListPOsArray as $SURPackingListPO) {
                // $tempSURPackingList = SURPackingList::where('po', '=', $SURPackingListPO)->first();
                //for SUR, have to truncate the first ten digits
                $tempSURPackingList = SURPackingList::where('po', '=', substr($SURPackingListPO,0,10))->first();
                if ($tempSURPackingList == null) {
                    //do not have packing list yet
                    array_push($notFoundPOs, $SURPackingListPO);
                } else {
                    //  array_push($SURPackingListPathArray, $tempSURPackingList->pathToFile);
                    // dd($tempSURPackingList->shipterms=='Ground');
//                    if ($tempSURPackingList->shipterms != 'Ground') {
//                        array_push($nonGroundPOs, $SURPackingListPO);
//                    }

                    switch ($tempSURPackingList->shipterms) {
                        case 'Ground':
                            array_push($groundSURPackingListPathArray,$tempSURPackingList->pathToFile);
                            array_push($groundPOs,$SURPackingListPO);
                            break;
                        case 'ND':
                            array_push($overnightSURPackingListPathArray,$tempSURPackingList->pathToFile);
                            array_push($overnightPos,$SURPackingListPO);
                            break;
                        case '2D':
                            array_push($secondDaySURPackingListPathArray,$tempSURPackingList->pathToFile);
                            array_push($secondDayPos,$SURPackingListPO);
                            break;
                        case '3D':
                            array_push($thirdDaySURPackingListPathArray,$tempSURPackingList->pathToFile);
                            array_push($thirdDayPos,$SURPackingListPO);
                            break;
                    }



                }

            }
            if (count($notFoundPOs) > 0) {
                $notFoundPOsReturnString = '';
                foreach ($notFoundPOs as $notFoundPO) {
                    $notFoundPOsReturnString .= $notFoundPO . '<br>';
                }
                return View::make('parsepdfSUR-exportpdf-output')->with(array('response' => '<p style="color:red;">The below POs are missing:</p><br>' . $notFoundPOsReturnString));


            } else {
//                $groundPOs=array();
//                $overnightPos=array();
//                $secondDayPos=array();
//                $thirdDayPos=array();
//                $groundSURPackingListPathArray = array();
//                $overnightSURPackingListPathArray = array();
//                $secondDaySURPackingListPathArray = array();
//                $thirdDaySURPackingListPathArray = array();
                //different types of shipment variables just listed above to remember
                //return a download file..we have all Packing lists

                //if only type of ship method exists for the request group of POs ... only return the path to the file and not the queries

                if(empty($overnightSURPackingListPathArray)&&empty($secondDaySURPackingListPathArray)&&empty($thirdDaySURPackingListPathArray)){
                    //beginning of making a group of packing list, need to now make for seperate types
                    $pdf = new PDFMerger;
                    foreach ($groundSURPackingListPathArray as $SURPackingListPath) {
                        $pdf->addPDF($SURPackingListPath);
                    }
                    $tempoutputpath = 'output' . '-' . time() . '.pdf';
                    $outputpath = public_path() . '/SURpos/' . $tempoutputpath;
                    $pdf->merge('file', $outputpath);
                    $outputpath = 'SURpos/' . $tempoutputpath;
                    //end of making a group of SURPackingList

                    $data['response'] = $this->createDownloadLink($outputpath,'Click here to download the generated packing lists (All Ground)');
                    // dd($nonGroundPOs);
                    //    $data['outputpath'] = $outputpath;



                    return View::make('parsepdfSUR-exportpdf-output', $data);

                }else{
                    //response to build
                    $response='';

                    //other types of ship methods exist so return associated queries with them

                    //begin ground
                    if(empty($groundSURPackingListPathArray)==false){
                        $pdf = new PDFMerger;
                        foreach ($groundSURPackingListPathArray as $SURPackingListPath) {
                            $pdf->addPDF($SURPackingListPath);
                        }
                        $tempoutputpath = 'output' . '-' . time() .'ground'. '.pdf';
                        $outputpath = public_path() . '/SURpos/' . $tempoutputpath;
                        $pdf->merge('file', $outputpath);
                        $outputpath = 'SURpos/' . $tempoutputpath;
                        $response.='Query for Ground POs: <br><br>';
                        $response.=$this->joinPurchaseOrders($groundPOs).'<br><br>';
                        $response.=$this->createDownloadLink($outputpath,'Click here to download the Ground packing lists');


                    }


                    //end ground


                    //begin overnight
                    if(empty($overnightSURPackingListPathArray)==false){
                        $pdf = new PDFMerger;
                        foreach ($overnightSURPackingListPathArray as $SURPackingListPath) {
                            $pdf->addPDF($SURPackingListPath);
                        }
                        $tempoutputpath = 'output' . '-' . time().'on' . '.pdf';
                        $outputpath = public_path() . '/SURpos/' . $tempoutputpath;
                        $pdf->merge('file', $outputpath);
                        $outputpath = 'SURpos/' . $tempoutputpath;
                        $response.='Query for Overnight POs: <br><br>';
                        $response.=$this->joinPurchaseOrders($overnightPos).'<br><br>';
                        $response.=$this->createDownloadLink($outputpath,'Click here to download the Overnight packing lists');


                    }
                    //end overnight


                    //begin second day
                    if(empty($secondDaySURPackingListPathArray)==false){
                        $pdf = new PDFMerger;
                        foreach ($secondDaySURPackingListPathArray as $SURPackingListPath) {
                            $pdf->addPDF($SURPackingListPath);
                        }
                        $tempoutputpath = 'output' . '-' . time() .'2nd'. '.pdf';
                        $outputpath = public_path() . '/SURpos/' . $tempoutputpath;
                        $pdf->merge('file', $outputpath);
                        $outputpath = 'SURpos/' . $tempoutputpath;
                        $response.='Query for 2nd Day POs: <br><br>';
                        $response.=$this->joinPurchaseOrders($secondDayPos).'<br><br>';
                        $response.=$this->createDownloadLink($outputpath,'Click here to download the 2nd Day packing lists');

                    }

                    //end second day


                    //begin third day
                    if(empty($thirdDaySURPackingListPathArray)==false){
                        $pdf = new PDFMerger;
                        foreach ($thirdDaySURPackingListPathArray as $SURPackingListPath) {
                            $pdf->addPDF($SURPackingListPath);
                        }
                        $tempoutputpath = 'output' . '-' . time() .'3rd'. '.pdf';
                        $outputpath = public_path() . '/SURpos/' . $tempoutputpath;
                        $pdf->merge('file', $outputpath);
                        $outputpath = 'SURpos/' . $tempoutputpath;
                        $response.='Query for 3rd Day POs: <br><br>';
                        $response.=$this->joinPurchaseOrders($thirdDayPos).'<br><br>';
                        $response.=$this->createDownloadLink($outputpath,'Click here to download the 3rd Day packing lists');

                    }

                    //end third day
                    $data['response']=$response;
                    return View::make('parsepdfSUR-exportpdf-output', $data);
                }



                //beginning of making a group of packing list, need to now make for seperate types
//                $pdf = new PDFMerger;
//                foreach ($SURPackingListPathArray as $SURPackingListPath) {
//                    $pdf->addPDF($SURPackingListPath);
//                }
//                $tempoutputpath = 'output' . '-' . time() . '.pdf';
//                $outputpath = public_path() . '/SURpos/' . $tempoutputpath;
//                $pdf->merge('file', $outputpath);
//                $outputpath = 'SURpos/' . $tempoutputpath;
//                //end of making a group of SURPackingList
//
//                $data['nonGroundPos'] = $nonGroundPOs;
                // dd($nonGroundPOs);
                //    $data['outputpath'] = $outputpath;






// REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options

            }

        } else {

            return View::make('parsepdfSUR-exportpdf-input')->with(array('response' => '<p style="color:red;">Please add a list of POs below.</p>'));

        }
    }

    function createDownloadLink($path,$message){
        return '<a href="'.$path.'" target="_blank">'.$message.'</a><br><br><hr>';
    }

    function deleteDBForm()
    {
        return View::make('deleteSURDBForm');

    }

    function deleteDBSubmission()
    {
        $result = Input::get('delete');
        if ($result != 'Y-E-S') {
            $data['response'] = '<span style="color:red">DB reset has failed. Y-E-S was not entered.</span>';
            return View::make('deleteSURDBForm', $data);
        } else {

            SURPackingList::truncate();
            $this->rrmdir(storage_path() . '/SURpos');
            $this->rrmdir(public_path() . '/SURpos');

            $data['response'] = '<span style="color:red">All items in the Surlatable DB have been cleared.</span>';
            return View::make('deleteSURDBForm', $data);
        }

    }

    private function joinSURParsePO($ordersArray)
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
