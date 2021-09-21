<?php
namespace Procure\Application\Service\PR\Output;

use Application\Entity\NmtInventoryItemPicture;
use Procure\Application\Service\Output\AbstractProcureDocSaveAsPdf;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Application\Service\PR\Output\Pdf\PdfCSS;
use Procure\Domain\GenericDoc;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrSaveAsPdfWithPicture extends AbstractProcureDocSaveAsPdf
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\ProcureDocSaveAsInterface::saveAs()
     */
    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter, $offset = null, $limit = null)
    {
        if ($this->getBuilder() == null) {
            return null;
        }

        if (! $doc instanceof PRDoc) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        $doc->refreshDoc();

        // Set Header
        $params = [
            "docNumber" => $doc->getSysNumber(),
            "doc" => $doc
        ];
        $this->getBuilder()->buildHeader($params);

        $html = PdfCSS::STYLE1;
        $header = $html . '<div class="docType">Purchase Request</div><br>';
        $header = $header . \sprintf('<span class="docDetail">No.         : %s - Date: %s</span><br>', ucfirst($doc->getDocNumber()), $doc->getSubmittedOn());
        $header = $header . \sprintf('<span class="docDetail">Ref.        : %s</span><br>', $doc->getSysNumber());
        $header = $header . \sprintf('<span class="docDetail">Created by  : %s</span><br>', $doc->getCreatedByName());

        $details = $html . '<table  class="table-fill">
        <tr class="text-left" style="color:black;">
        <th class="text-left" style="width: 30px;">#</th>
        <th class="text-left" style="width: 40%;">Item</th>
        <th class="text-left" style="width: 80pt;">Picture</th>
        <th class="text-left" style="width: 5px;">Unit</th>
        <th class="text-left">Requested</th>
        <th class="text-left">Reveived</th>
        <th class="text-left">Open</th>
          </tr>';
        $n = 0;

        $decimalNo = 0;
        $curency = array(
            "USD",
            "THB",
            "EUR"
        );
        foreach ($doc->getRowCollection() as $r) {

            $n ++;

            /**
             *
             * @var PRRow $r ;
             * @var PRRowSnapshot $row ;
             */
            $item_thumbnail = $this->getItemPic($r->getItem());
            $row = $formatter->format($r->makeSnapshot());

            $itemDetails = \sprintf('<span class="itemDetail">Id: %s</span>', $row->getItemSKU());

            if ($row->getItemManufacturerCode() !== null) {
                $itemDetails = $itemDetails . \sprintf('<br><span class="itemDetail">Code: %s</span>', $row->getItemManufacturerCode());
            }

            if ($row->getLastCurrency() !== null) {

                if (in_array($row->getLastCurrency(), $curency)) {
                    $decimalNo = 2;
                }

                $itemDetails = $itemDetails . \sprintf('<br><span class="itemDetail">%s</span>', $row->getLastCurrency());
            }

            if ($row->getLastUnitPrice() !== null) {
                $itemDetails = $itemDetails . \sprintf('<span class="itemDetail"  style="font-size:9.5pt;"> %s</span>', number_format($row->getLastUnitPrice(), $decimalNo));
            }

            if ($row->getLastVendorName() !== null) {
                $itemDetails = $itemDetails . \sprintf('<br><span class="itemDetail" style="font-size:9.5pt;">%s</span>', $row->getLastVendorName());
            }

            // $itemDetails = $itemDetails . \sprintf('<br><span class="itemDetail">Code: %s</span>', $row->getItemSysNumber());

            $details .= '<tr style="font-size: 9.5px; border: 0px solid black;">';
            $details .= sprintf('<td class="text-left">%s<br></td>', $n);
            $details .= sprintf('<td class="text-left"><div>%s</div>%s</td>', strtoupper($row->getItemName()), $itemDetails);
            $details .= sprintf('<td><img src="%s" width="80" height="80"></td>', $item_thumbnail);

            $details .= sprintf('<td class="text-left">%s</td>', $row->getDocUnit());
            $details .= sprintf('<td class="text-left">%s</td>', $row->getDocQuantity());
            $details .= sprintf('<td class="text-left">%s</td>', $row->getPostedGrQuantity());
            $details .= sprintf('<td class="text-left">%s</td>', $row->getDocQuantity() - $row->getPostedGrQuantity());

            $details .= '</tr>';
        }

        $details .= '</table>';

        $params = [
            "doc" => $doc,
            "header" => $header,
            "details" => $details
        ];
        $this->getBuilder()->buildBody($params);

        // created footer and export
        $this->getBuilder()->buildFooter();
    }

    /**
     *
     * @param int $id
     * @return mixed|string|mixed
     */
    private function getItemPic($id)
    {

        /** @var \Application\Entity\NmtInventoryItemPicture $pic ;*/
        $pic = $this->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryItemPicture')
            ->findOneBy(array(
            'item' => $id,
            'isActive' => 1
        ));

        $thumbnail_file = '/images/no-pic1.jpg';
        if ($pic instanceof NmtInventoryItemPicture) {

            $thumbnail_file = "/thumbnail/item/" . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();
            $thumbnail_file = str_replace('\\', '/', $thumbnail_file); // Important for UBUNTU

            return $thumbnail_file;
        }

        return $thumbnail_file;
    }
}