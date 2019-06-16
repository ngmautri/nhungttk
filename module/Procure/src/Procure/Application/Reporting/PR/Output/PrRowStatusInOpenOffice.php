<?php
namespace Procure\Application\Reporting\PR\Output;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowStatusInOpenOffice extends PrRowStatusOutputStrategy
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

            if ($pr_row_entity->getPr() == null) {
                continue;
            }

            $dto = $this->createDTOFrom($pr_row_entity);

            if ($dto == null) {
                continue;
            }

            $dto->poQuantity = $a['po_qty'];
            $dto->postedPoQuantity = $a['posted_po_qty'];

            $dto->grQuantity = $a['gr_qty'];
            $dto->postedGrQuantity = $a['posted_gr_qty'];

            $dto->stockGrQuantity = $a['stock_gr_qty'];
            $dto->postedStockGrQuantity = $a['posted_stock_gr_qty'];

            $dto->apQuantity = $a['ap_qty'];
            $dto->postedApQuantity = $a['posted_ap_qty'];

            $dto->prAutoNumber = $pr_row_entity->getPr()->getPrAutoNumber();
            $dto->prNumber = $pr_row_entity->getPr()->getPrNumber();
            $dto->prName = $pr_row_entity->getPr()->getPrName();
            $dto->prSubmittedOn = $pr_row_entity->getPr()->getSubmittedOn();
            $dto->prYear = $a['pr_year'];
            $dto->itemName = $a['item_name'];

            $output[] = $dto;
        }

        return $output;
    }
}
