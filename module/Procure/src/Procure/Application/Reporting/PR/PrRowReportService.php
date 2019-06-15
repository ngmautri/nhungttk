<?php
namespace Procure\Application\Reporting\PR;

use Application\Service\AbstractService;
use Zend\Escaper\Escaper;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowReportService extends AbstractService
{

    /**
     *
     * @var DoctrinePRListRepository;
     */
    private $prListRespository;

    public function createGird($is_active, $pr_year, $balance, $sort_by, $sort, $limit, $offset)
    {
        $list = $this->getPrListRespository()->getAllPrRow($is_active, $pr_year, $balance, $sort_by, $sort, $limit, $offset);
        $total_records = count($list);

        $a_json_final = array();
        $a_json = array();
        $a_json_row = array();
        $escaper = new Escaper();

        if ($total_records > 0) {

            $count = 0;
            foreach ($list as $a) {

                /**@var \Application\Entity\NmtProcurePrRow $pr_row_entity ;*/
                $pr_row_entity = $a[0];

                if ($pr_row_entity->getItem() == null) {
                    continue;
                }

                $item_detail = "/inventory/item/show1?token=" . $pr_row_entity->getItem()->getToken() . "&checksum=" . $pr_row_entity->getItem()->getChecksum() . "&entity_id=" . $pr_row_entity->getItem()->getId();
                if ($pr_row_entity->getItem()->getItemName() !== null) {
                    $onclick = "showJqueryDialog('Detail of Item: " . $escaper->escapeJs($pr_row_entity->getItem()
                        ->getItemName()) . "','1200',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                } else {
                    $onclick = "showJqueryDialog('Detail of Item: " . ($pr_row_entity->getItem()->getItemName()) . "','1200',$(window).height()-50,'" . $item_detail . "','j_loaded_data', true);";
                }

                $count ++;
                $a_json_row["row_number"] = $count;

                $a_json_row["pr_number"] = $pr_row_entity->getPr()->getPrNumber() . '<a style="" target="blank"  title="' . $pr_row_entity->getPr()->getPrNumber() . '" href="/procure/pr/show?token=' . $pr_row_entity->getPr()->getToken() . '&entity_id=' . $pr_row_entity->getPr()->getId() . '&checksum=' . $pr_row_entity->getPr()->getChecksum() . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</span></a>';

                if ($pr_row_entity->getPr()->getSubmittedOn() !== null) {
                    $a_json_row['pr_submitted_on'] = date_format($pr_row_entity->getPr()->getSubmittedOn(), 'd-m-y');
                    // $a_json_row ['pr_submitted_on'] = $a ['submitted_on'];
                } else {
                    $a_json_row['pr_submitted_on'] = '';
                }

                $a_json_row["row_id"] = $pr_row_entity->getId();
                $a_json_row["row_token"] = $pr_row_entity->getToken();
                $a_json_row["row_checksum"] = $pr_row_entity->getChecksum();

                $a_json_row["item_sku"] = '<span title="' . $pr_row_entity->getItem()->getItemSku() . '">' . substr($pr_row_entity->getItem()->getItemSku(), 0, 5) . '</span>';

                if (strlen($pr_row_entity->getItem()->getItemName()) < 35) {
                    $a_json_row["item_name"] = $pr_row_entity->getItem()->getItemName() . '<a style="cursor:pointer;color:blue"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;....&nbsp;&nbsp;</a>';
                } else {
                    $a_json_row["item_name"] = substr($pr_row_entity->getItem()->getItemName(), 0, 30) . '<a style="cursor:pointer;;color:blue"  item-pic="" id="' . $pr_row_entity->getItem()->getId() . '" item_name="' . $pr_row_entity->getItem()->getItemName() . '" title="' . $pr_row_entity->getItem()->getItemName() . '" href="javascript:;" onclick="' . $onclick . '" >&nbsp;&nbsp;...&nbsp;&nbsp;</a>';
                }

                $a_json_row["quantity"] = $pr_row_entity->getQuantity();
                $a_json_row["confirmed_balance"] = $a['confirmed_balance'];

                if (strlen($a['last_vendor_name']) < 10) {
                    $a_json_row["vendor_name"] = $a['last_vendor_name'];
                } else {
                    $a_json_row["vendor_name"] = '<span title="' . $a['last_vendor_name'] . '">' . substr($a['last_vendor_name'], 0, 8) . '...</span>';
                }

                if ($a['last_vendor_unit_price'] !== null) {
                    $a_json_row["vendor_unit_price"] = number_format($a['last_vendor_unit_price'], 2);
                } else {
                    $a_json_row["vendor_unit_price"] = 0;
                }

                $a_json_row["currency"] = $a['last_currency'];

                $received_detail = "/inventory/item-transaction/pr-row?pr_row_id=" . $pr_row_entity->getId();

                if ($pr_row_entity->getItem()->getItemName() !== null) {
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . $escaper->escapeJs($pr_row_entity->getItem()
                        ->getItemName()) . "','1200',$(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
                } else {
                    $onclick1 = "showJqueryDialog('Receiving of Item: " . ($pr_row_entity->getItem()->getItemName()) . "','1200', $(window).height()-100,'" . $received_detail . "','j_loaded_data', true);";
                }

                if ($a['total_received'] > 0) {
                    $a_json_row["total_received"] = '<a style="color: #337ab7;" href="javascript:;" onclick="' . $onclick1 . '" >' . $a['total_received'] . '</a>';
                } else {
                    $a_json_row["total_received"] = "";
                }
                $a_json_row["buying"] = $a['po_quantity_draft'] + $a['po_quantity_final'];

                if ($pr_row_entity->getProject() !== null) {
                    $a_json_row["project_id"] = $pr_row_entity->getProject()->getId();
                } else {
                    $a_json_row["project_id"] = "";
                }

                if (strlen($pr_row_entity->getRemarks()) < 20) {
                    $a_json_row["remarks"] = $pr_row_entity->getRemarks();
                } else {
                    $a_json_row["remarks"] = '<span title="' . $pr_row_entity->getRemarks() . '">' . substr($pr_row_entity->getRemarks(), 0, 15) . '...</span>';
                }
                $a_json_row["fa_remarks"] = $pr_row_entity->getRemarks();
                $a_json_row["receipt_date"] = "";
                $a_json_row["vendor"] = "";
                $a_json_row["vendor_id"] = "";

                $a_json[] = $a_json_row;
            }

            $a_json_final['data'] = $a_json;
            $a_json_final['totalRecords'] = $total_records;
        }

        return $a_json_final;
    }

    /**
     *
     * @return \Procure\Infrastructure\Persistence\DoctrinePRListRepository
     */
    public function getPrListRespository()
    {
        return $this->prListRespository;
    }

    /**
     *
     * @param \Procure\Infrastructure\Persistence\DoctrinePRListRepository $prListRespository
     */
    public function setPrListRespository($prListRespository)
    {
        $this->prListRespository = $prListRespository;
    }
}
