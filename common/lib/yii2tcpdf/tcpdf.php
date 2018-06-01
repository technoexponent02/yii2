<?php

//============================================================+
// File name   : tcpdf.php
// Version     : 1.0.2
// Begin       : 2016-02-27
// Last Update : 2016-02-27
// Author      : Eakkabin Jaikeawma - DriveSoft.Co LTD - www.drivesoft.co
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------

# TCPDF Library
require_once(dirname(__FILE__).'/tcpdf_library.php'); // ?????????? Class TCPDF

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

class TCPDF extends TCPDF_LIBRARY {
    
    public $object         = null;
    
    public $fontName       = 'freeserif';  // ????????
    public $fontSize       = 10;           // ?????????
    
    public $author         = 'Eakkabin Jaikeawma <CoachMaxz>';
    public $title          = 'Report Title';
    
    public $pageSize       = 'A4';        // ?????????? A4
    public $encoding       = 'UTF-8';     // ?????????? UTF-8
    public $orientation    = 'P';         // ?????? P ???? | L ???
    
	public function __construct($orientation = null) {
        parent::__construct(!empty($orientation) ? $orientation : $this->orientation, PDF_UNIT, $this->pageSize, true, $this->encoding, false);
        $this->init(); // ตั้งค่า
    }
	
    public function init() {
        
        // ??????? font ????????
        $this->SetFont($this->fontName, '', $this->fontSize);
        
        // Author
        $this->SetAuthor($this->author);
        $this->SetTitle($this->title);
        
        // ????? Header Report
        $this->setHeaderFont(array($this->fontName, '', $this->fontSize));
        $this->SetHeaderData('', 0, 'Report', 'Created : ' . date('Y-m-d H:i:s'));
        
        // ????? Footer Report
        $this->setFooterFont(array($this->fontName, '', $this->fontSize));
        
        // ???????????????? Font ??????????????
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        //?????????????????
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM); // ?????????????????????????
        
    }
	
	// Page header
    /* public function Header() {
        
        // Set font
        $this->SetFont($this->fontName, '', $this->fontSize);
        
        // Logo
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->Image($image_file, 15, 5, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->Ln(5);
        
        // Title
        $this->Cell(0, 5, $this->title, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        $this->Cell(0, 10, 'Created : ' . date('Y/m/d H:i:s',time()), 0, false, 'R', 0, '', 0, false, 'M', 'M');
        
    } */
    
    // Page footer
    /* public function Footer() {
        
        // Set font
        $this->SetFont($this->fontName, '', $this->fontSize);
        
        $this->SetY(-15);
        
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        
    } */
    
}