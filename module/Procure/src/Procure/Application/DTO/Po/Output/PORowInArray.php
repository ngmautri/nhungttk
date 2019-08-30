<?php
namespace Procure\Application\DTO\Po\Output;

use Zend\Escaper\Escaper;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowInArray extends PORowOutputStrategy
{

   /**
    * 
    * {@inheritDoc}
    * @see \Procure\Application\DTO\Ap\Output\APDocRowOutputStrategy::createOutput()
    */
    public function createOutput($result)
    {
        if ($result == null)
            return null;

        /**@var \Application\Entity\FinVendorInvoiceRow $result ;*/

        if ($result->getInvoice() == null || $result->getItem() == null) {
            continue;
        }

        $dto = $this->createDTOFrom($result);
        
        $escaper = new Escaper();
        
        $item_detail = sprintf("/inventory/item/show1?token=%s&checksum=%s&entity_id=%s", $result->getItem()->getToken(), $result->getItem()->getChecksum(), $result->getItem()->getId());
        
        $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($result->getItem()
            ->getItemName()) . "','1600',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
            
            if (strlen($result->getItem()->getItemName()) < 35) {
                $dto->itemName = $result->getItem()->getItemName() . '<a style="cursor:pointer;color:#337ab7"  item-pic="" id="' . $result->getItem()->getId() . '" item_name="' . $result->getItem()->getItemName() . '" title="' . $result->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;(i)&nbsp;</a>';
            } else {
                $dto->itemName = substr($result->getItem()->getItemName(), 0, 30) . '<a style="cursor:pointer;color:#337ab7"  item-pic="" id="' . $result->getItem()->getId() . '" item_name="' . $result->getItem()->getItemName() . '" title="' . $result->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;(i)&nbsp;</a>';
            }
            

        $output = $this->getOutput();
        $output[] = $dto;
        $this->output = $output;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\DTO\Warehouse\Transaction\Output\TransactionRowOutputStrategy::createRowOutput()
     */
    public function createRowOutputFromSnapshot($result)
    {
        if ($result == NULL)
            return;

        $output = $this->getOutput();
        $output[] = $result;
        $this->output = $output;
    }

}
