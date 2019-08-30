<?php
namespace Procure\Application\DTO\Ap\Output;

use Zend\Escaper\Escaper;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApDocRowInOpenOffice extends APDocRowOutputStrategy
{

   

    public function createRowOutputFromSnapshot($result)
    {}
    
   
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
                
                $link = sprintf('<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/show?token=%s&entity_id=%s&checkum=%s">&nbsp;&nbsp;(i)&nbsp;</a>', $pr_row_entity->getPr()->getPrAutoNumber(), $pr_row_entity->getPr()->getToken(), $pr_row_entity->getPr()->getId(), $pr_row_entity->getPr()->getChecksum());
                
                $dto->prNumber = $pr_row_entity->getPr()->getPrNumber() . $link;
                $dto->prName = $pr_row_entity->getPr()->getPrName() . $link;
                
                if ($pr_row_entity->getPr()->getSubmittedOn() !== null) {
                    $dto->prSubmittedOn = date_format($pr_row_entity->getPr()->getSubmittedOn(), "d-m-y");
                } else {
                    $dto->prSubmittedOn = null;
                }
                $dto->prYear = $a['pr_year'];
                // $dto->itemName = $a['item_name'];
                $dto->itemSKU = $pr_row_entity->getItem()->getItemSku();
                
                $escaper = new Escaper();
                
                $item_detail = sprintf("/inventory/item/show1?token=%s&checksum=%s&entity_id=%s", $pr_row_entity->getItem()->getToken(), $pr_row_entity->getItem()->getChecksum(), $pr_row_entity->getItem()->getId());
                
                $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($pr_row_entity->getItem()
                    ->getItemName()) . "','1600',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                    
                    if (strlen($pr_row_entity->getItem()->getItemName()) < 35) {
                        $dto->itemName = $pr_row_entity->getItem()->getItemName() . '<a style="cursor:pointer;color:#337ab7"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;(i)&nbsp;</a>';
                    } else {
                        $dto->itemName = substr($pr_row_entity->getItem()->getItemName(), 0, 30) . '<a style="cursor:pointer;color:#337ab7"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;(i)&nbsp;</a>';
                    }
                    
                    $decimalNo = 0;
                    $curency = array(
                        "USD",
                        "THB",
                        "EUR"
                    );
                    if (in_array($a['currency_iso3'], $curency)) {
                        $decimalNo = 2;
                    }
                    $dto->lastCurrency = $a['currency_iso3'];
                    
                    $dto->lastVendor = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $a['vendor_name']);
                    if ($a['unit_price'] !== null) {
                        
                        $dto->lastUP = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', number_format($a['unit_price'], $decimalNo));
                    } else {
                        $dto->lastUP = "";
                    }
                    
                    $dto->lastCurrency = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $a['currency_iso3']);
                    
                    $output[] = $dto;
            }
            
            return $output;
    }

}
