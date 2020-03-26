<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\AssetCountingItem;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class AssetCountingItemTable
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function get($id)
    {
        $id = (int) $id;

        $rowset = $this->tableGateway->select(array(
            'id' => $id
        ));
        $row = $rowset->current();
        if (! $row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /*
     * $sql = '(SELECT t1.*, t2.name as asset_name, t2.tag as tag
     * FROM mla_asset_counting_items as t1
     * LEFT JOIN mla_asset AS t2
     * ON t2.id = t1.asset_id
     * WHERE t1.counting_id = '. $id) as T2;
     */
    public function getCountedItems($id, $category_id)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = "SELECT
	mla_asset.*,
	mla_asset_counting_items.counting_id,
	mla_asset_counting_items.location as counted_location,
	mla_asset_counting_items.counted_by,
	mla_asset_counting_items.verified_by,
	mla_asset_counting_items.acknowledged_by,
	mla_asset_counting_items.counted_on,

	mla_asset_pics.asset_picture_id
FROM mla_asset

LEFT JOIN
(
SELECT
*
FROM mla_asset_counting_items

WHERE mla_asset_counting_items.counting_id= " . $id;

        $sql = $sql . "					
)
AS mla_asset_counting_items
ON mla_asset_counting_items.asset_id = mla_asset.id

/*picture*/
            LEFT JOIN 
            (
	           SELECT
					mla_asset_pics.asset_id,
					MAX(mla_asset_pics.id) AS asset_picture_id
				FROM mla_asset_pics
				GROUP BY mla_asset_pics.asset_id
            )
            AS mla_asset_pics
            ON mla_asset_pics.asset_id = mla_asset.id


WHERE 1
AND mla_asset.category_id=" . $category_id;

        $sql = $sql . " ORDER BY mla_asset_counting_items.counted_on DESC ";

        ;

        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     *
     * @param unknown $id
     * @param unknown $category_id
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getTotalCounted($id)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = "
SELECT
	Count(CASE WHEN mla_asset_counting_items.counted_on is null THEN  mla_asset_counting_items.counted_on ELSE 0 END) AS total_counted
FROM mla_asset_counting_items
WHERE 1
AND mla_asset_counting_items.counting_id=" . $id;

        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        if (count($resultSet) > 0) {
            return $resultSet->current()->total_counted;
        } else {
            return 0;
        }
    }

    public function getTotalToCount($category_id)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = "
SELECT
	count(*) as total_to_count
FROM mla_asset
WHERE mla_asset.category_id=" . $category_id;

        $sql = $sql . ";";

        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        if ($resultSet->count() > 0) {
            return $resultSet->current()->total_to_count;
        } else {
            return 0;
        }
    }

    public function add(AssetCountingItem $input)
    {
        $data = array(
            'counting_id' => $input->counting_id,
            'asset_id' => $input->asset_id,
            'location' => $input->location,
            'counted_by' => $input->counted_by,
            'verified_by' => $input->verified_by,
            'acknowledged_by' => $input->acknowledged_by,
            'counted_on' => date('Y-m-d H:i:s')
        );
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function update(AssetCountingItem $input, $id)
    {
        $data = array(
            'counting_id' => $input->counting_id,
            'asset_id' => $input->asset_id,
            'location' => $input->location,
            'counted_by' => $input->counted_by,
            'verified_by' => $input->verified_by,
            'acknowledged_by' => $input->acknowledged_by,
            'counted_on' => date('Y-m-d H:i:s')
        );

        $where = 'id = ' . $id;
        $this->tableGateway->update($data, $where);
    }

    public function delete($id)
    {
        $where = 'id = ' . $id;
        $this->tableGateway->delete($where);
    }

    public function isAssetCounted($counting_id, $asset_id)
    {
        $adapter = $this->tableGateway->adapter;

        $where = array(
            'asset_id=?' => $asset_id,
            'counting_id=?' => $counting_id
        );

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from(array(
            't1' => 'mla_asset_counting_items'
        ));
        $select->where($where);

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        if ($results->count() > 0) {
            return true;
        } else {
            return false;
        }
    }
}