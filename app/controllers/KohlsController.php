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
        $new_path = storage_path() . '/pos/' . $individualFileName . '/';
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
                    //     $end_directory='';
                    //                $new_filename = $end_directory.str_replace('.pdf', '', $filePath).'_'.$i.".pdf";

                    $new_filename = storage_path() . "/pos/" . $individualFileName . '/' . $individualFileName . '-' . $i . '.pdf';
                    $newPackingList->pathToFile = $new_filename;
                    $new_pdf->Output($new_filename, "F");
                    $newPackingList->save();
                    echo "Page " . $i . " split into " . $new_filename . "<br />\n";
                } catch (Exception $e) {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }
            }
        }
    }

    public function index()
    {


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
            $file = Input::file('packinglist');
            $name = Input::file('packinglist')->getClientOriginalName();
            $file_name = pathinfo($name, PATHINFO_FILENAME); // file
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

            $pdf = new FPDI();
            $pagecount = $pdf->setSourceFile($file);
//          print_r($pagecount);
//            print_r($arrayOfPos);
            // dd(storage_path());
            $this->split_pdf($file, $name, $file_name, $arrayOfPos, public_path());
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

                }

            }
            if (count($notFoundPOs) > 0) {
                return View::make('parsepdfKohls-exportpdf-output')->with(array('response' => '<p style="color:red;">POs are missing</p>'));

            } else {
                //return a download file..we have all Packing lists
                $pdf = new PDFMerger;
                foreach ($packingListPathArray as $packingListPath) {
                    $pdf->addPDF($packingListPath);
                }


                $pdf->merge('download', 'output.pdf');

// REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options

            }
            //Stores the inputted cmmfs and quantities in Session variables incase user wants to use Back button to redo query

        } else {

            return View::make('parsepdfKohls-exportpdf-input')->with(array('response' => '<p style="color:red;">Please add a list of POs below.</p>'));

        }
    }


}
