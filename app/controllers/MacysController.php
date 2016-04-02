<?php

class MacysController extends \BaseController
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


    function split_pdf($filePath, $fileName, $individualFileName, $arrayOfPos, $end_directory = false)
    {
        $new_path = storage_path() . '/Macyspos';


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
            $tempPackingList = MacysPackingList::where('po', '=', $arrayOfPos[$i - 1])->first();

            if ($tempPackingList == null) {
                $new_pdf = new FPDI();
                $new_pdf->AddPage();
                $new_pdf->setSourceFile($filePath);
                $newPackingList = new MacysPackingList();
                $newPackingList->po = trim($arrayOfPos[$i - 1]);
                $new_pdf->useTemplate($new_pdf->importPage($i));

                try {
                    $new_filename = storage_path() . '/Macyspos/' . $arrayOfPos[$i - 1] . '.pdf';

                    $newPackingList->pathToFile = $new_filename;
                    $new_pdf->Output($new_filename, "F");
                    $newPackingList->save();

                } catch (Exception $e) {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }
            }
        }
    }

    function split_multi_pdf($arrayOfFiles)
    {

        $new_path = storage_path() . '/Macyspos';


        if (!is_dir($new_path)) {
            // Will make directories under end directory that don't exist
            // Provided that end directory exists and has the right permissions
            mkdir($new_path, 0777, true);
        }
        foreach ($arrayOfFiles as $file) {
            $tempArrayOfPos = array();
            $tempArrayOfPos = $this->getArrayOfPOs($file);
            $pdf = new FPDI();
            $pagecount = $pdf->setSourceFile($file); // How many pages?
            for ($i = 1; $i <= $pagecount; $i++) {
                $singleItem = $tempArrayOfPos[$i - 1];
                //   dd($singleItem);
                $tempPackingList = MacysPackingList::where('po', '=', $singleItem['PO'])->first();

                if ($tempPackingList == null) {
                    $new_pdf = new FPDI();
                    $new_pdf->AddPage();
                    $new_pdf->setSourceFile($file);
                    $newPackingList = new MacysPackingList();
                    $newPackingList->po = trim($singleItem['PO']);
                    $newPackingList->shipterms = trim($singleItem['shipterms']);
                    $new_pdf->useTemplate($new_pdf->importPage($i));

                    try {
                        $new_filename = storage_path() . '/Macyspos/' . $singleItem['PO'] . '.pdf';
                        $newPackingList->pathToFile = $new_filename;
                        $new_pdf->Output($new_filename, "F");
                        $newPackingList->save();

                    } catch (Exception $e) {
                        echo 'Caught exception: ', $e->getMessage(), "\n";
                    }
                }
            }

        }


    }


    public function index()
    {


    }

    function checkIfArrayIndexContainsOrder($haystack){

        if ((strpos($haystack, 'Purchase') !== false)&&(strpos($haystack, 'Order') !== false)&&(strpos($haystack, 'Number') !== false)) {
            return true;
        }
        else {
            return false;
        }
    }
    function contains($haystack,$needle){
        return strpos($haystack,$needle)!==false;
    }
    function getArrayOfPOs($file)
    {
        $returnArray = array();

        $parser = new \Smalot\PdfParser\Parser();

        $pdf = $parser->parseFile($file);
        $pages = $pdf->getPages();

        foreach ($pages as $page) {
            $text = nl2br($page->getText());



            $tempPDF = explode('<br />', $text);

          //  dd($tempPDF);
            foreach($tempPDF as $tempPDFArrayIndex){
                //        dd($tempPDFArrayIndex);
                if($this->checkIfArrayIndexContainsOrder($tempPDFArrayIndex)){
                    // array index contains order #
                    // echo 'temppdfvalue-iftrue:'.$tempPDFArrayIndex.'<br>';
                    $getPO = explode(':', $tempPDFArrayIndex);
                    //dd($getPO);
                }else{
                    //array index does not contain order # ..keep trying
                    //          echo 'temppdfvalue-iffalse:'.$tempPDFArrayIndex.'<br>';
                }
            }

            // $getPO = explode(':', $tempPDF[6]);
            // dd($getPO[1]);
            $data['PO'] = trim($getPO[1]);
            $isGround = $this->checkIfGround($text);
            if ($isGround) {
                $data['shipterms'] = "Ground";
            } else {
                $data['shipterms'] = "Not Ground";
            }
            //    $PO = trim($getPO[1]);
            array_push($returnArray, $data);

        }
//dd($returnArray);
        return $returnArray;

    }

    function checkIfGround($haystack)
    {

//        if (strpos($haystack, 'FEDX') !== false) {
//            return true;
//        }
//        else {
//            return false;
//        }
return true;

        // always true for now since no way on packinglist to tell..may be updated later
    }

    public function parseGetPDF()
    {
        return View::make('parsepdfMacys-importpdf-input');

    }

    public function parsePostPDF()
    {

//        $validator = Validator::make(Input::all(),
//            array(
//                'packinglist' => 'required'
//            )
//        );
//
//
//        if (!$validator->fails()) {
            if(Input::file('packinglist')[0]!=NULL){
            //validation passes
            $files = Input::file('packinglist');
            $this->split_multi_pdf($files);

            return View::make('parsepdfMacys-importpdf-output');
        } else {
//validation fails
            return View::make('parsepdfMacys-importpdf-input')->with(array('response' => '<p style="color:red;">Please select a packing list pdf to parse.</p>'));

        }


    }

    public function retrieveGetPDF()
    {

        return View::make('parsepdfMacys-exportpdf-input');
    }

    public function retrievePostPDF()
    {

        $validator = Validator::make(Input::all(),
            array(
                'packinglist' => 'required'
            )
        );


        if (!$validator->fails()) {

     //       if(Input::file('packinglist')[0]!=NULL){
            include(app_path() . '/includes/PDFMerger.php');
            $notFoundPOs = array();
            $nonGroundPOs = array();
            $packingListPOsArray = array();
            $packingListPathArray = array();
            $packingListPOs = trim(Input::get('packinglist'));
            Session::flash('packinglist', $packingListPOs);
            $packingListPOsArray = explode(PHP_EOL, $packingListPOs);

            foreach ($packingListPOsArray as $packingListPO) {
                $tempPackingList = MacysPackingList::where('po', '=', $packingListPO)->first();
                if ($tempPackingList == null) {
                    //do not have packing list yet
                    array_push($notFoundPOs, $packingListPO);
                } else {
                    array_push($packingListPathArray, $tempPackingList->pathToFile);
                    // dd($tempPackingList->shipterms=='Ground');
                    if ($tempPackingList->shipterms != 'Ground') {
                        array_push($nonGroundPOs, $packingListPO);
                    }


                }

            }
            if (count($notFoundPOs) > 0) {
                $notFoundPOsReturnString = '';
                foreach ($notFoundPOs as $notFoundPO) {
                    $notFoundPOsReturnString .= $notFoundPO . '<br>';
                }
                return View::make('parsepdfMacys-exportpdf-output')->with(array('response' => '<p style="color:red;">The below POs are missing:</p><br>' . $notFoundPOsReturnString));


            } else {
                //return a download file..we have all Packing lists
                $pdf = new PDFMerger;
                foreach ($packingListPathArray as $packingListPath) {
                    $pdf->addPDF($packingListPath);
                }
                $tempoutputpath = 'output' . '-' . time() . '.pdf';
                $outputpath = public_path() . '/Macyspos/' . $tempoutputpath;
                $pdf->merge('file', $outputpath);
                $outputpath = 'Macyspos/' . $tempoutputpath;
                $data['nonGroundPos'] = $nonGroundPOs;
                // dd($nonGroundPOs);
                $data['outputpath'] = $outputpath;
                return View::make('parsepdfMacys-exportpdf-output', $data);


// REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options

            }

        } else {

            return View::make('parsepdfMacys-exportpdf-input')->with(array('response' => '<p style="color:red;">Please add a list of POs below.</p>'));

        }
    }


    function deleteDBForm()
    {
        return View::make('deleteMacysDBForm');

    }

    function deleteDBSubmission()
    {
        $result = Input::get('delete');
        if ($result != 'Y-E-S') {
            $data['response'] = '<span style="color:red">DB reset has failed. Y-E-S was not entered.</span>';
            return View::make('deleteMacysDBForm', $data);
        } else {

            MacysPackingList::truncate();
            if (Input::get('deleteLocal') != null) {
                $this->rrmdir(storage_path() . '/Macyspos');
                $this->rrmdir(public_path() . '/Macyspos');
            }
            $data['response'] = '<span style="color:red">All items in the Macys.com DB have been cleared.</span>';
            return View::make('deleteMacysDBForm', $data);
        }

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


