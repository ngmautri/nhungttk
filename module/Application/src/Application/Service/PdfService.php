<?php

namespace Application\Service;

use Zend\Permissions\Acl\Acl;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\Controller\ControllerManager;
use MLA\Service\AbstractService;
use Application\Utility\MLAPdf;

/*
 * @author nmt
 *
 */
class PdfService extends AbstractService {
	protected $moduleManager;
	protected $controllerManager;
	public function initAcl(Acl $acl) {
		// TODO
	}
	public function createPdf() {
		
		define ('PDF_HEADER_LOGO', 'mascot.gif');
		
		include_once ROOT.'\vendor\tcpdf\tcpdf.php';
		// create new PDF document
		
		// create new PDF document
		$pdf = new MLAPdf ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		
		// set document information
		$pdf->SetCreator ( PDF_CREATOR );
		$pdf->SetAuthor ( 'Mascot Laos ' );
		$pdf->SetTitle ( 'Purchase request' );
		$pdf->SetSubject ( 'TCPDF Tutorial' );
		$pdf->SetKeywords ( 'TCPDF, PDF, example, test, guide' );
		
	// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(20, 28, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'B', 14);

// add a page
$pdf->AddPage();

$pdf->Write(0, 'Purchase Request', '', 0, 'C', true, 0, false, false, 0);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Write(0, 'Date: ', '', 0, 'C', true, 0, false, false, 0);

$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 9);

$pdf->Write(0, 'PR Number: ', '', 0, 'L', true, 0, false, false, 0);

$pdf->Ln(1);
$pdf->Write(0, 'Requester: Nguyen Mau Tri', '', 0, 'L', true, 0, false, false, 0);
$pdf->Ln(1);
$pdf->Write(0, 'Department: Finace', '', 0, 'L', true, 0, false, false, 0);

$pdf->Ln(9);
// -----------------------------------------------------------------------------

$tbl = <<<EOD
<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <td rowspan="3">COL 1 - ROW 1<br />COLSPAN 3</td>
        <td>COL 2 - ROW 1</td>
        <td>COL 3 - ROW 1</td>
    </tr>
    <tr>
        <td rowspan="2">COL 2 - ROW 2 - COLSPAN 2<br />text line<br />text line<br />text line<br />text line</td>
        <td>COL 3 - ROW 2</td>
    </tr>
    <tr>
       <td>COL 3 - ROW 3</td>
    </tr>

</table>
EOD;

$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->Write(0, '- Reason: ', '', 0, 'L', true, 0, false, false, 0);
$pdf->Ln(15);

$pdf->Write(0, '- Check Budget: ', '', 0, 'L', true, 0, false, false, 0);
$pdf->Ln(25);

$tbl2 = <<<EOD
<table style="border: none; with=100%">
<thead>
<tr>
<td style="text-align: center">Requested by	</td>
<td style="text-align: center">Concured by HOD  
</td>
<td style="text-align: center">Concured by Finance Manager</td>
<td style="text-align: center">Approved by Managing Director</td>
</tr>
</thead>
</table>
EOD;
$pdf->Ln(9);
$pdf->writeHTML($tbl2, true, false, false, false, '');


// -----------------------------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_048.pdf', 'I');
	}
	
	public function savePRAsPdf(
			$requester,
			$department,
			$date,
			$pr_number,
			$pr_auto_number,
			$detail
		) {
	
		define ('PDF_HEADER_LOGO', ROOT.'\public\images\mascot.gif');
		
		include_once ROOT.'\vendor\tcpdf\tcpdf.php';
		// create new PDF document
	
		// create new PDF document
		$pdf = new MLAPdf ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
	
		// set document information
		$pdf->SetCreator ( PDF_CREATOR );
		$pdf->SetAuthor ( 'Mascot Laos ' );
		$pdf->SetTitle ( 'Purchase request ' . $pr_number);
		$pdf->SetSubject ( 'TCPDF Tutorial' );
		$pdf->SetKeywords ( 'TCPDF, PDF, example, test, guide' );
	
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
		// set margins
		$pdf->SetMargins(20, 33, 15);
		$pdf->SetHeaderMargin(9);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
	
		// ---------------------------------------------------------
	
		// set font
		$pdf->SetFont('helvetica', 'B', 14);
	
		// add a page
		$pdf->AddPage();
	
		$pdf->Write(0, 'Purchase Request', '', 0, 'C', true, 0, false, false, 0);
		$pdf->Ln(2);
		$pdf->SetFont('helvetica', 'I', 10);
		$pdf->Write(0, 'PR No.: ' .$pr_number , '', 0, 'C', true, 0, false, false, 0);
		$pdf->Write(0, 'Date: ' .$date , '', 0, 'C', true, 0, false, false, 0);
		
		$pdf->Ln(5);
		$pdf->SetFont('helvetica', '', 10);
	
		$pdf->Ln(1);
		$pdf->Write(0, 'Requester: '.$requester, '', 0, 'L', true, 0, false, false, 0);
		$pdf->Ln(1);
		$pdf->Write(0, 'Department: ' . $department, '', 0, 'L', true, 0, false, false, 0);
	
		$pdf->Ln(5);
		// -----------------------------------------------------------------------------
	
		$tbl = $detail;
		$pdf->SetFont('helvetica', '', 10);
		$pdf->writeHTML($tbl, true, false, false, false, '');
	
		$pdf->Write(0, '- Reason: ', '', 0, 'L', true, 0, false, false, 0);
		$pdf->Ln(15);
	
		$pdf->Write(0, '- Check Budget: ', '', 0, 'L', true, 0, false, false, 0);
		$pdf->Ln(25);
		
		$pdf->SetFont('helvetica', '', 9);
		$tbl2 = <<<EOD
<div style="border: none; with=100%">
<table style="border: none; with=100%">
<thead>
<tr>
<td style="text-align: center">Requested by	</td>
<td style="text-align: center">Concured by HOD
</td>
<td style="text-align: center">Concured by Finance Manager</td>
<td style="text-align: center">Approved by Managing Director</td>
</tr>
</thead>
</table>
		</div>
EOD;
		$pdf->Ln(9);
		$pdf->writeHTML($tbl2, true, false, false, false, '');
	
	
		// -----------------------------------------------------------------------------
	
		//Close and output PDF document
		$pdf->Output('PR.'.$pr_auto_number.'.pdf', 'I');
	}
	
	
	public function saveDNAsPdf(
			$detail
			) {
	
				define ('PDF_HEADER_LOGO', ROOT.'\public\images\mascot.gif');
	
				include_once ROOT.'\vendor\tcpdf\tcpdf.php';
				// create new PDF document
	
				// create new PDF document
				$pdf = new MLAPdf ( 'P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
	
				// set document information
				$pdf->SetCreator ( PDF_CREATOR );
				$pdf->SetAuthor ( 'Mascot Laos ' );
				//$pdf->SetTitle ( 'Purchase request ' . $pr_number);
				$pdf->SetSubject ( 'Delivery Note' );
				$pdf->SetKeywords ( 'Mascot Las, PDF, Delivery, Note' );
	
				// set default monospaced font
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
				// set margins
				$pdf->SetMargins(20, 33, 15);
				$pdf->SetHeaderMargin(9);
				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
				// set auto page breaks
				$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
				// set image scale factor
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
				// set some language-dependent strings (optional)
				if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
					require_once(dirname(__FILE__).'/lang/eng.php');
					$pdf->setLanguageArray($l);
				}
	
				// ---------------------------------------------------------
	
				// set font
				$pdf->SetFont('helvetica', 'B', 14);
	
				// add a page
				$pdf->AddPage();
	
				$pdf->Write(0, 'Delivery Note', '', 0, 'C', true, 0, false, false, 0);
				$pdf->Ln(2);
				$pdf->SetFont('helvetica', 'I', 10);
				//$pdf->Write(0, 'DN No.: ' .$pr_number , '', 0, 'C', true, 0, false, false, 0);
				//->Write(0, 'Date: ' .$date , '', 0, 'C', true, 0, false, false, 0);
	
				$pdf->Ln(5);
				$pdf->SetFont('helvetica', '', 10);
	
				$pdf->Ln(1);
				//$pdf->Write(0, 'Requester: '.$requester, '', 0, 'L', true, 0, false, false, 0);
				$pdf->Ln(1);
				//$pdf->Write(0, 'Department: ' . $department, '', 0, 'L', true, 0, false, false, 0);
	
				$pdf->Ln(5);
				// -----------------------------------------------------------------------------
	
				$tbl = $detail;
				$pdf->SetFont('helvetica', '', 10);
				$pdf->writeHTML($tbl, true, false, false, false, '');
	
				$pdf->Ln(5);
				$pdf->Write(0, 'Delivered By ', '', 0, 'L', true, 0, false, false, 0);
				
	
				// -----------------------------------------------------------------------------
	
				//Close and output PDF document
				$pdf->Output('Delivery Note.'.'1'.'.pdf', 'I');
	}
}