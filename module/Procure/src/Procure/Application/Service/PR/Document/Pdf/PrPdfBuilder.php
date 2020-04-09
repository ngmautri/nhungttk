<?php
namespace Application\Application\Service\Document\Pdf;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrPdfBuilder extends AbstractBuilder
{

    /**
     * 
     * {@inheritDoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::buildBody()
     */
    public function buildBody($params = null)
    {
        $pdf = $this->getPdf();

        // create some HTML content
        $tbl = <<<EOD
<h3 style="text-align: center">Purchase Request</h3>
<table cellpadding="1" cellspacing="1" border="1" style="text-align:center;">
<tr><td><img src="public/images/mascot.gif" border="0" height="41" width="41" /></td></tr>
<tr style="text-align:left;"><td>Just a test <img src="public/images/mascot.gif" border="0" height="41" width="41" align="top" /></td></tr>
</table>

EOD;

        // output the HTML content
        $pdf->SetFont('helvetica', '', 10);
        $pdf->writeHTML($detail, true, false, false, false, '');
        $pdf->SetFont('helvetica', '', 9);

        // $pdf->Image($pr_code, 170 , 5, 20, '', 'PNG', '', 'T', false, 100, '', false, false, 0, false, false, false);

        // QRCODE,H : QR-CODE Best error correction
        $pdf->write2DBarcode('/procure/pr/show?token=pGtKSwxXug_iF6E_1yXJnRGoao_Rw_ks&entity_id=310&checksum=405ea7dc6c17c272a184328a90015240', 'QRCODE,M', 20, 20, 20, 20, $style, 'N');
        $pdf->Text(170, 20, '');
    }

    public function buildFooter($params = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::buildHeader()
     */
    public function buildHeader($params = null)
    {
        $pdf = $this->getPdf();

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nguyen Mau Tri - MLA ');
        $pdf->SetTitle('Purchase Request ' . "");
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

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
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once (dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // set style for barcode
        $style = array(
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(
                0,
                0,
                0
            ),
            'bgcolor' => false, // array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('helvetica', 'B', 14);

        // add a page
        $pdf->AddPage();

        $pdf->Ln(2);
        $pdf->SetFont('helvetica', '', 10);

        $pdf->Ln(2);
        // -----------------------------------------------------------------------------

        // $tbl = $detail. $pr_code;
        // $pdf->SetFont ( 'helvetica', '', 10 );
        // $pdf->writeHTML ( $tbl, true, false, false, false, '' );

        // $pdf->SetFont ( 'helvetica', '', 9 );

        // -----------------------------------------------------------------------------
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::getDocument()
     */
    public function getDocument()
    {
        $pdf = $this->getPdf();
        
        // Close and output PDF document
        $pdf->Output('report.pdf', 'I');
    }
}

