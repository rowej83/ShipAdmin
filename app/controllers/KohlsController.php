<?php

class KohlsController extends \BaseController
{

    function __construct()
    {
        //   include(app_path().'/includes/fpdf_tpl.php');

//        include(app_path().'/includes/tcpdi.php');
        //    include(app_path().'/includes/tcpdf_filters.php');
        // include(app_path().'/includes/tcpdi_parser.php');

    }

    function split_pdf($filePath, $fileName, $individualFileName, $arrayOfPos, $end_directory = false)
    {
//        require_once('fpdf/fpdf.php');
//        require_once('fpdi/fpdi.php');

        //  $end_directory = $end_directory ? $end_directory : './';
        //   $new_path = preg_replace('/[\/]+/', '/', $end_directory.'/'.substr($filename, 0, strrpos($filename, '/')));
        $new_path = storage_path() . '/Kohlspos';


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
            $tempPackingList = PackingList::where('po', '=', $arrayOfPos[$i - 1])->first();

            if ($tempPackingList == null) {
                $new_pdf = new FPDI();
                $new_pdf->AddPage();
                $new_pdf->setSourceFile($filePath);
                $newPackingList = new PackingList();
                $newPackingList->po = trim($arrayOfPos[$i - 1]);
                $new_pdf->useTemplate($new_pdf->importPage($i));

                try {
                    //        $end_directory='';
//                                    $new_filename = str_replace('.pdf', '', $filePath).'_'.$i.".pdf";
                    $new_filename = storage_path() . '/Kohlspos/' . $arrayOfPos[$i - 1] . '.pdf';
                    //  $new_filename = storage_path() . "/pos/" .trim($arrayOfPos[$i - 1]).'.pdf';

                    //      dd($new_filename);
                    $newPackingList->pathToFile = $new_filename;
                    $new_pdf->Output($new_filename, "F");
                    $newPackingList->save();
                    // echo "Page " . $i . " split into " . $new_filename . "<br />\n";
                } catch (Exception $e) {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }
            }
        }
    }

    function split_multi_pdf($arrayOfFiles)
    {
//        require_once('fpdf/fpdf.php');
//        require_once('fpdi/fpdi.php');

        //  $end_directory = $end_directory ? $end_directory : './';
        //   $new_path = preg_replace('/[\/]+/', '/', $end_directory.'/'.substr($filename, 0, strrpos($filename, '/')));

        //  dd(count($arrayOfFiles));
        $new_path = storage_path() . '/Kohlspos';


        if (!is_dir($new_path)) {
            // Will make directories under end directory that don't exist
            // Provided that end directory exists and has the right permissions
            mkdir($new_path, 0777, true);
        }
        foreach ($arrayOfFiles as $file) {
            $tempArrayOfPos=array();
$tempArrayOfPos=$this->getArrayOfPOs($file);
            $pdf = new FPDI();
            $pagecount = $pdf->setSourceFile($file); // How many pages?
            for ($i = 1; $i <= $pagecount; $i++) {
                $singleItem=$tempArrayOfPos[$i-1];
             //   dd($singleItem);
                $tempPackingList = PackingList::where('po', '=', $singleItem['PO'])->first();

                if ($tempPackingList == null) {
                    $new_pdf = new FPDI();
                    $new_pdf->AddPage();
                    $new_pdf->setSourceFile($file);
                    $newPackingList = new PackingList();
                    $newPackingList->po = trim($singleItem['PO']);
                    $newPackingList->shipterms=trim($singleItem['shipterms']);
                    $new_pdf->useTemplate($new_pdf->importPage($i));

                    try {
                        //        $end_directory='';
//                                    $new_filename = str_replace('.pdf', '', $filePath).'_'.$i.".pdf";
                        $new_filename = storage_path() . '/Kohlspos/' .$singleItem['PO'] . '.pdf';
                        //  $new_filename = storage_path() . "/pos/" .trim($arrayOfPos[$i - 1]).'.pdf';

                        //      dd($new_filename);
                        $newPackingList->pathToFile = $new_filename;
                        $new_pdf->Output($new_filename, "F");
                        $newPackingList->save();
                        // echo "Page " . $i . " split into " . $new_filename . "<br />\n";
                    } catch (Exception $e) {
                        echo 'Caught exception: ', $e->getMessage(), "\n";
                    }
                }
            }

        }

//        $pdf = new FPDI();
//        $pagecount = $pdf->setSourceFile($filePath); // How many pages?
////dd($pagecount);
        // Split each page into a new PDF
    }


    public function index()
    {


    }

    function getArrayOfPOs($file)
    {
        $returnArray = array();

        $parser = new \Smalot\PdfParser\Parser();

        $pdf = $parser->parseFile($file);
        $pages = $pdf->getPages();
      //  $tempArrayOfPos=array();
        foreach ($pages as $page) {
            $text = nl2br($page->getText());

            $tempPDF = explode('<br />', $text);
//dd($tempPDF);

            $getPO = explode(':', $tempPDF[10]);
            $data['PO']=trim($getPO[1]);
            $isGround=$this->checkIfGround($text);
          if($isGround){
              $data['shipterms']="Ground";
          }else{
              $data['shipterms']="Not Ground";
          }
        //    $PO = trim($getPO[1]);
            array_push($returnArray, $data);

        }
//dd($returnArray);
        return $returnArray;

    }
    function checkIfGround($haystack)
    {
        return strpos($haystack, 'Continental US - Standard Ground') !== false;
    }

    public function parseGetPDF()
    {
        return View::make('parsepdfKohls-importpdf-input');

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
            $files = Input::file('packinglist');
            $this->split_multi_pdf($files);
//            $file = Input::file('packinglist');
//            $name = Input::file('packinglist')->getClientOriginalName();
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
            //  $queryString = $this->joinKohlsParsePO($arrayOfPos);
            //    $data['POs'] = $arrayOfPos;
            //   $returnPOString='';
//            foreach($arrayOfPos as $returnPO){
//                $returnPOString.=$returnPO.'<br>';
//            }
//            $data['POs']=$returnPOString;
//            $data['totalOfPOs'] = $totalOfPos;
//            $data['queryString'] = $queryString;
            return View::make('parsepdfKohls-importpdf-output');
        } else {
//validation fails
            return View::make('parsepdfKohls-importpdf-input')->with(array('response' => '<p style="color:red;">Please select a packing list pdf to parse.</p>'));

        }


    }

    public function retrieveGetPDF()
    {

        return View::make('parsepdfKohls-exportpdf-input');
    }

    public function retrievePostPDF()
    {

        $validator = Validator::make(Input::all(),
            array(
                'packinglist' => 'required'
            )
        );


        if (!$validator->fails()) {
            include(app_path() . '/includes/PDFMerger.php');
            $notFoundPOs = array();
            $nonGroundPOs=array();
            $packingListPOsArray = array();
            $packingListPathArray = array();
            $packingListPOs = trim(Input::get('packinglist'));
            Session::flash('packinglist', $packingListPOs);
            $packingListPOsArray = explode(PHP_EOL, $packingListPOs);

            foreach ($packingListPOsArray as $packingListPO) {
                $tempPackingList = PackingList::where('po', '=', $packingListPO)->first();
                if ($tempPackingList == null) {
                    //do not have packing list yet
                    array_push($notFoundPOs, $packingListPO);
                } else {
                    array_push($packingListPathArray, $tempPackingList->pathToFile);
                   // dd($tempPackingList->shipterms=='Ground');
                   if($tempPackingList->shipterms!='Ground'){
                       array_push($nonGroundPOs,$packingListPO);
                   }


                }

            }
            if (count($notFoundPOs) > 0) {
               $notFoundPOsReturnString='';
                foreach($notFoundPOs as $notFoundPO){
                    $notFoundPOsReturnString.=$notFoundPO.'<br>';
                }
                return View::make('parsepdfKohls-exportpdf-output')->with(array('response' => '<p style="color:red;">The below POs are missing:</p><br>'.$notFoundPOsReturnString));


            } else {
                //return a download file..we have all Packing lists
                $pdf = new PDFMerger;
                foreach ($packingListPathArray as $packingListPath) {
                    $pdf->addPDF($packingListPath);
                }
$tempoutputpath='output'.'-'.time().'.pdf';
$outputpath=public_path().'/Kohlspos/'.$tempoutputpath;
                $pdf->merge('file', $outputpath);
$outputpath='Kohlspos/'.$tempoutputpath;
                $data['nonGroundPos']=$nonGroundPOs;
               // dd($nonGroundPOs);
                $data['outputpath']=$outputpath;
return View::make('parsepdfKohls-exportpdf-output',$data);



// REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options

            }

        } else {

            return View::make('parsepdfKohls-exportpdf-input')->with(array('response' => '<p style="color:red;">Please add a list of POs below.</p>'));

        }
    }


    function deleteDBForm()
    {
        return View::make('deleteKohlsDBForm');

    }

    function deleteDBSubmission()
    {
        $result = Input::get('delete');
        if ($result != 'Y-E-S') {
            $data['response'] = '<span style="color:red">DB reset has failed. Y-E-S was not entered.</span>';
            return View::make('deleteKohlsDBForm', $data);
        } else {

            PackingList::truncate();
            if (Input::get('deleteLocal') != null) {
                $this->rrmdir(storage_path() . '/Kohlspos');
                $this->rrmdir(public_path() . '/Kohlspos');
            }
            $data['response'] = '<span style="color:red">All items in the Kohls DB have been cleared.</span>';
            return View::make('deleteKohlsDBForm', $data);
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
