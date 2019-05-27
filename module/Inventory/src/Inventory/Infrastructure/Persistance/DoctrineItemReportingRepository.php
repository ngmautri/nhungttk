<?php
namespace Inventory\Infrastructure\Persistence;

use Application\Infrastructure\Persistence\AbstractDoctrineRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineItemReportingRepository extends AbstractDoctrineRepository implements ItemReportingRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemReportingRepositoryInterface::getLastAPRows()
     */
    public function getLastAPRows($limit = 100, $offset = 0)
    {
        $sql_tmp = "
SELECT
	fin_vendor_invoice_row.*
	FROM fin_vendor_invoice_row
    where fin_vendor_invoice_row.current_state='finalInvoice'
	ORDER BY fin_vendor_invoice_row.created_on DESC LIMIT  %s";

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = sprintf($sql_tmp, $limit);

        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRow', 'fin_vendor_invoice_row');
            $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemReportingRepositoryInterface::getAllItemWithSerial()
     */
    public function getAllItemWithSerial($itemId = null)
    {
        $sql = 'SELECT
nmt_inventory_item_serial.id AS serial_id,
nmt_inventory_item_serial.serial_number as serial_no,
nmt_inventory_item_serial.serial_number_1 as serial_no1,
nmt_inventory_item_serial.serial_number_2 as serial_no2,
            
nmt_inventory_item_serial.mfg_name,
nmt_inventory_item_serial.mfg_description,
nmt_inventory_item_serial.remarks as serial_remarks,
nmt_inventory_item_serial.mfg_serial_number,
nmt_inventory_item_serial.mfg_model,
nmt_inventory_item_serial.mfg_model1,
nmt_inventory_item_serial.mfg_model2,
nmt_inventory_item.*
FROM nmt_inventory_item
LEFT JOIN nmt_inventory_item_serial
ON nmt_inventory_item_serial.item_id = nmt_inventory_item.id
            
Where 1 and nmt_inventory_item.is_active=1
';
        if ($itemId > 0) {
            $sql = $sql . ' and nmt_inventory_item.id = ' . $itemId;
        }

        $stmt = $this->doctrineEM->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemReportingRepositoryInterface::getMostOrderItems()
     */
    public function getMostOrderItems($limit = 50, $offset = 0)
    {
        $sql_tmp = "
SELECT
	nmt_inventory_item.*,
  	COUNT(CASE WHEN nmt_procure_pr_row.is_active =1 THEN (nmt_procure_pr_row.id) ELSE NULL END) AS total_pr_row
FROM nmt_inventory_item
LEFT JOIN nmt_procure_pr_row
ON nmt_procure_pr_row.item_id = nmt_inventory_item.id
group by nmt_inventory_item.id
order by COUNT(CASE WHEN nmt_procure_pr_row.is_active =1 THEN (nmt_procure_pr_row.id) ELSE NULL END) DESC LIMIT %s";

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = sprintf($sql_tmp, $limit);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $rsm->addScalarResult("total_pr_row", "total_pr_row");
            $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemReportingRepositoryInterface::getLastCreatedItems()
     */
    public function getLastCreatedItems($limit = 100, $offset = 0)
    {
        $sql_tmp = "
select
    nmt_inventory_item.*
    From nmt_inventory_item
order by nmt_inventory_item.created_on desc LIMIT %s";

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = sprintf($sql_tmp, $limit);

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItem', 'nmt_inventory_item');
            $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemReportingRepositoryInterface::getRandomItem()
     */
    public function getRandomItem()
    {
        $sql_tmp = "
SELECT * FROM nmt_inventory_item_picture ORDER BY RAND() LIMIT 0,1 ";
        // $sql=sprintf($sql_tmp,$limit);
        $sql = $sql_tmp;
        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryItemPicture', 'nmt_inventory_item_picture');
            $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistence\ItemReportingRepositoryInterface::getMostValueItems()
     */
    public function getMostValueItems($rate = 8100, $limit = 100, $offset = 0)
    {
        $sql_tmp = "
SELECT
            
fin_vendor_invoice_row.unit_price*fin_vendor_invoice.exchange_rate as lak_unit_price,
fin_vendor_invoice_row.*
            
FROM fin_vendor_invoice_row
            
LEFT JOIN fin_vendor_invoice
ON fin_vendor_invoice.id = fin_vendor_invoice_row.invoice_id
            
WHERE fin_vendor_invoice_row.is_active=1
group by fin_vendor_invoice_row.item_id
ORDER BY (fin_vendor_invoice_row.unit_price*fin_vendor_invoice.exchange_rate) DESC
LIMIT %s
";

        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }

        $sql = sprintf($sql_tmp, $limit);

        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\FinVendorInvoiceRow', 'fin_vendor_invoice_row');
            $rsm->addScalarResult("lak_unit_price", "lak_unit_price");
            $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
            
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Infrastructure\Persistence\ItemReportingRepositoryInterface::getLastCreatedPrRow()
     */
    public function getLastCreatedPrRow($limit = 100, $offset = 0)
    {
        $sql_tmp = "
SELECT
	nmt_procure_pr_row.*
FROM nmt_procure_pr_row
ORDER BY nmt_procure_pr_row.created_on DESC LIMIT %s";
        
        if ($offset > 0) {
            $sql_tmp = $sql_tmp . " OFFSET " . $offset;
        }
        
        $sql = sprintf($sql_tmp, $limit);
        
        try {
            $rsm = new ResultSetMappingBuilder($this->doctrineEM);
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtProcurePrRow', 'nmt_procure_pr_row');
            $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
            $result = $query->getResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
    

   
}
