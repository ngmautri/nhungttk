<?php
namespace Procure\Model\Pr;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PdfStrategy extends DownloadStrategyAbstract
{

    protected $doctrineEM;

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
        $details = '<h3 style="text-align: center">Purchase Request (Testing only)</h3>';
        $details .= $target->getPrAutoNumber() . '<br>';
        $details .= $target->getPrName() . '<br><br>';

        $details .= '
        <table style="font-size: 9.5px; border: 0.5px solid black; width:100%">
        <tr style="font-size: 9.5px; border: 0.5px solid black;">
        <td style="width: 30px; border: 0.5px solid black;">#</td>
        <td style="width: 42%; border: 0.5px solid black;">Item</td>
        <td style="width: 40px; border: 0.5px solid black;">Q\'ty</td>
        <td style="width: 40px; border: 0.5px solid black;">Unit</td>
        <td>Picture</td>
        <td style="width: auto; border: 0.5px solid black;">Remarks</td>
        </tr>
        ';

        $n = 0;
        foreach ($rows as $r) {

            $n ++;

            /**@var \Application\Entity\NmtProcurePrRow $a ;*/
            $a = $r[0];
            
            $pic_id = $r['picture_id'];
            $criteria = array(
                'id' => $pic_id
            );

            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItemPicture')->findOneBy($criteria);
            $pic_location = null;
            if ($entity !== null) {

                $pic = new \Application\Entity\NmtInventoryItemPicture();
                $pic = $entity;
                $pic_folder = getcwd() . "/data/inventory/picture/item/" . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();

                /**
                 * Important! for UBUNTU
                 */
                $pic_folder = str_replace('\\', '/', $pic_folder);
                $pic_location = $pic_folder;
            }

            $pic_url = sprintf('<img src="%s" border="0" height="150" width="150">', $pic_location);

            // '<span style="color:graytext; font-size:9pt"> item #'. $a->getItem()->getSysNumber() .'</span>'

            $details .='<tr style="font-size: 9.5px; border: 0.5px solid black;">';
            $details .= sprintf('<td style="font-size: 9.5px; border: 0.5px solid black;">%s</td>', $n);
            $details .= sprintf('<td style="font-size: 9.5px; border: 0.5px solid black;">%s</td>', $a->getItem()->getItemName());
            $details .= sprintf('<td style="font-size: 9.5px; border: 0.5px solid black;">%s</td>', $a->getQuantity());
            $details .= sprintf('<td style="font-size: 9.5px; border: 0.5px solid black;">%s</td>', $a->getRowUnit());
            $details .= sprintf('<td style="font-size: 9.5px; border: 0.5px solid black;">%s</td>', $pic_url);
            $details .= sprintf('<td style="font-size: 9.5px; border: 0.5px solid black;">%s</td>', $a->getRemarks());
            $details .='</tr>';
        }

        $details.='</table>';
        $content = $pdfService->printPrPdf($details, $image_file);
        return $content;
    }

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM($doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

}