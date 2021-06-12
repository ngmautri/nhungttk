<?php
namespace Inventory\Infrastructure\Doctrine\Helper;

use Application\Entity\NmtInventoryItem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Domain\Item\Statistics\ItemStatistics;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemStatistisHelper
{

    /*
     * |=============================
     * |Statistics
     * |
     * |=============================
     */
    static public function createStatistics(EntityManager $doctrineEM, NmtInventoryItem $item)
    {
        $stat = new ItemStatistics();

        $stat->setTotalPicture($item->getPictureList()
            ->count());

        $stat->setTotalSerial($item->getSerialNoList()
            ->count());

        // ===============
        /*
         * $stat->setTotalPR($item->getPrList()
         * ->count());
         *
         * $stat->setTotalQO($item->getQoList()
         * ->count());
         *
         * $stat->setTotalPO($item->getPoList()
         * ->count());
         *
         * $stat->setTotalAP($item->getApList()
         * ->count());
         */

        return $stat;
    }

    static public function createItemStatistics(EntityManager $doctrineEM, $id)
    {
        $sql1 = sprintf("(select count(*) from nmt_inventory_item_picture where item_id = %s) as total_picture", $id);
        $sql2 = sprintf("(select count(*) from nmt_inventory_item_variant where item_id = %s) as total_variant", $id);

        $sql3 = sprintf("(select count(*) from nmt_procure_pr_row where item_id = %s) as total_pr", $id);
        $sql4 = sprintf("(select count(*) from nmt_procure_qo_row where item_id = %s) as total_qo", $id);
        $sql5 = sprintf("(select count(*) from nmt_procure_po_row where item_id = %s) as total_po", $id);
        $sql6 = sprintf("(select count(*) from fin_vendor_invoice_row where item_id = %s) as total_ap", $id);
        $sql7 = sprintf("(select count(*) from nmt_inventory_association_item where main_item_id = %s) as total_association", $id);
        $sql8 = sprintf("(select count(*) from nmt_inventory_item_serial where item_id = %s) as total_serial", $id);

        $f = 'select %s, %s, %s, %s, %s, %s, %s,%s';
        $sql = \sprintf($f, $sql1, $sql2, $sql3, $sql4, $sql5, $sql6, $sql7, $sql8);

        // echo $sql;

        $rsm = new ResultSetMappingBuilder($doctrineEM);

        $rsm->addScalarResult("total_picture", "total_picture");
        $rsm->addScalarResult("total_variant", "total_variant");
        $rsm->addScalarResult("total_pr", "total_pr");
        $rsm->addScalarResult("total_qo", "total_qo");
        $rsm->addScalarResult("total_po", "total_po");
        $rsm->addScalarResult("total_ap", "total_ap");
        $rsm->addScalarResult("total_association", "total_association");
        $rsm->addScalarResult("total_serial", "total_serial");

        $query = $doctrineEM->createNativeQuery($sql, $rsm);
        $result = $query->getSingleResult();

        $stat = new ItemStatistics();

        $stat->setTotalPicture($result['total_picture']);
        $stat->setTotalVariant($result['total_variant']);

        $stat->setTotalPR($result['total_pr']);
        $stat->setTotalQO($result['total_qo']);
        $stat->setTotalPO($result['total_po']);
        $stat->setTotalAP($result['total_ap']);
        $stat->setTotalAssosiation($result['total_association']);
        $stat->setTotalSerial($result['total_serial']);

        return $stat;
    }
}
