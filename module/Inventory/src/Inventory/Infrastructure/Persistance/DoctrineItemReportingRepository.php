<?php
namespace Inventory\Infrastructure\Persistance;

use Doctrine\ORM\EntityManager;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineItemReportingRepository implements ItemReportingRepositoryInterface
{

    /**
     *
     * @var EntityManager
     */
    private $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Infrastructure\Persistance\ItemReportingRepositoryInterface::getAllItemWithSerial()
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
    
    
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM($doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
