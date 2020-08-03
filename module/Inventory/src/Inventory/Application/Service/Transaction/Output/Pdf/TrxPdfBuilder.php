<?php
namespace Inventory\Application\Service\Transaction\Output\Pdf;

use Application\Application\Service\Document\Pdf\AbstractBuilder;
use Inventory\Domain\Transaction\GenericTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxPdfBuilder extends AbstractBuilder
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::buildBody()
     */
    public function buildBody($params = null)
    {
        $pdf = $this->getPdf();

        $detail = "";
        if (isset($params["details"])) {
            $detail = $params["details"];
        }

        /**
         *
         * @var GenericTrx $doc ;
         */
        $doc = "";
        if (isset($params["doc"])) {
            $doc = $params["doc"];
        }

        $detail1 = sprintf("Transaction Date: %s.<br>", $doc->getMovementDate());
        $detail1 = $detail1 . sprintf("Transaction Type: %s.<br>", $doc->getMovementType());

        $pdf->writeHTML($detail1, true, false, false, false, '');

        // output the HTML content
        $pdf->SetFont('helvetica', '', 10);
        $pdf->writeHTML($detail, true, false, false, false, '');
        $pdf->SetFont('helvetica', '', 9);

        // $pdf->Image($pr_code, 170 , 5, 20, '', 'PNG', '', 'T', false, 100, '', false, false, 0, false, false, false);

        // QRCODE,H : QR-CODE Best error correction
        // $pdf->write2DBarcode('/procure/pr/show?token=pGtKSwxXug_iF6E_1yXJnRGoao_Rw_ks&entity_id=310&checksum=405ea7dc6c17c272a184328a90015240', 'QRCODE,M', 20, 20, 20, 20, $style, 'N');
        // $pdf->Text(170, 20, '');
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::getDocument()
     */
    public function getDocument()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Application\Service\Document\DocumentBuilderInterface::buildFooter()
     */
    public function buildFooter($params = null)
    {
        $pdf = $this->getPdf();

        // Close and output PDF document
        $pdf->Output('report.pdf', 'I');
    }

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
        $pdf->SetTitle('Purchase Order ' . "");
        $pdf->SetSubject('PO');
        $pdf->SetKeywords('MLA, po, example, test, guide');

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(18, 33, 0, false);
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
}
