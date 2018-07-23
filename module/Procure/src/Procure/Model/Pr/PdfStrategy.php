<?php
namespace Procure\Model\Pr;

use Application\Entity\NmtProcurePr;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Application\Service\PdfService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PdfStrategy extends DownloadStrategyAbstract
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Model\Pr\DownloadStrategyAbstract::doDownload()
     */
    public function doDownload($target, $rows)
    {
        $pdfService = new \Application\Service\PdfService();

        
        /**@var \Application\Entity\NmtProcurePr $target ;*/
        
        $image_file = '';
        $details = '<h3 style="text-align: center">Purchase Request</h3>';
        $details.= $target->getPrAutoNumber() .'<br>';
        $details.= $target->getPrName() .'<br>';
        $details.='<hr>';
        
        $n=0;
        foreach ($rows as $r) {

            $n++;
            
            /**@var \Application\Entity\NmtProcurePrRow $a ;*/
            $a = $r[0];
            $details.= $n. '. ' . $a->getItem()->getItemName() ."<br>";
            $details.= '<span style="color:graytext; font-size:9pt"> item #'. $a->getItem()->getSysNumber() ."</span><br>";
            $details.='<hr>';
         }
        
        $content = $pdfService->printPrPdf($details, $image_file);
        return $content;
    }
}