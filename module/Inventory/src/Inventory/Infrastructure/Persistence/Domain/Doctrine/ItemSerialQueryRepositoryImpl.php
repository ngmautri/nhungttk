<?php
namespace Inventory\Infrastructure\Persistence\Domain\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Inventory\Domain\Item\Serial\Factory\ItemSerialFactory;
use Inventory\Domain\Item\Serial\Repository\ItemSerialQueryRepositoryInterface;
use Inventory\Infrastructure\Persistence\Domain\Doctrine\Helper\ItemSerialHelper;
use Inventory\Infrastructure\Persistence\Domain\Doctrine\Mapper\ItemSerialMapper;
use Inventory\Infrastructure\Persistence\SQL\Filter\ItemSerialSqlFilter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialQueryRepositoryImpl extends AbstractDoctrineRepository implements ItemSerialQueryRepositoryInterface
{

    public function getlist(ItemSerialSqlFilter $filter)
    {
        if ($filter == null) {
            throw new \InvalidArgumentException('Filter not given');
        }
        $rows = ItemSerialHelper::getRows($this->getDoctrineEM(), $filter);
        return ItemSerialHelper::createRowsGenerator($this->getDoctrineEM(), $rows);
    }

    public function getlistTotal(ItemSerialSqlFilter $filter)
    {
        if ($filter == null) {
            throw new \InvalidArgumentException('Filter not given');
        }
        return ItemSerialHelper::getTotalRows($this->getDoctrineEM(), $filter);
    }

    public function getOfInvoice($invoiceId)
    {}

    public function getOfItem($itemId)
    {}

    public function getOfPOGoodReceipt($grId)
    {}

    public function getVersion($id, $token = null)
    {}

    public function getVersionArray($id, $token = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Serial\Repository\ItemSerialQueryRepositoryInterface::getByTokenId()
     */
    public function getByTokenId($id, $token)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $doctrineEM = $this->getDoctrineEM();
        $rootEntityDoctrine = $doctrineEM->getRepository('\Application\Entity\NmtInventoryItemSerial')->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = ItemSerialMapper::createSerialSnapshot($rootEntityDoctrine, null);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootEntity = ItemSerialFactory::contructFromDB($rootSnapshot);
        return $rootEntity;
    }
}
