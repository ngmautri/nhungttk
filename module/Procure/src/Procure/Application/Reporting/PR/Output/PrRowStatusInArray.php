<?php
namespace Procure\Application\Reporting\PR\Output;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowStatusInArray extends PrRowStatusOutputStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy::createOutput()
     */
    public function createOutput($result)
    {
        if (count($result) == 0)
            return null;

        $output = array();

        foreach ($result as $a) {

            /**@var \Application\Entity\NmtProcurePrRow $pr_row_entity ;*/
            $pr_row_entity = $a[0];

            if ($pr_row_entity->getPr() == null || $pr_row_entity->getItem() == null) {
                continue;
            }

            $dto = $this->createDTOFrom($pr_row_entity);

            if ($dto == null) {
                continue;
            }

            $dto->poQuantity = number_format($a['po_qty'], 2);
            $dto->postedPoQuantity = number_format($a['posted_po_qty'], 2);

            $dto->grQuantity = number_format($a['gr_qty'], 2);
            $dto->postedGrQuantity = number_format($a['posted_gr_qty'], 2);

            $dto->stockGrQuantity = number_format($a['stock_gr_qty'], 2);
            $dto->postedStockGrQuantity = number_format($a['posted_stock_gr_qty'], 2);

            $dto->apQuantity = number_format($a['ap_qty'], 2);
            $dto->postedApQuantity = number_format($a['posted_ap_qty'], 2);

            $dto->prAutoNumber = $pr_row_entity->getPr()->getPrAutoNumber();
            $dto->prNumber = $pr_row_entity->getPr()->getPrNumber();
            $dto->prName = $pr_row_entity->getPr()->getPrName();

            if ($pr_row_entity->getPr()->getSubmittedOn() !== null) {
                $dto->prSubmittedOn = date_format($pr_row_entity->getPr()->getSubmittedOn(), "d-m-y");
            } else {
                $dto->prSubmittedOn = null;
            }
            $dto->prYear = $a['pr_year'];
            $dto->itemName = $a['item_name'];
            $dto->itemSKU = $pr_row_entity->getItem()->getItemSku();

            $output[] = $dto;
        }

        return $output;
    }
}
