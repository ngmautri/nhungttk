<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Inventory\Model\SparepartMovement;

class SparepartMovementsTable
{

    protected $tableGateway;

    private $movement_period_SQL = "
select
	mla_sparepart_movements.sparepart_id,
    sum(mla_sparepart_movements.quantity) as total_quantity,

    mla_sparepart_movements.flow,
    mla_sparepart_movements.quantity,
	year(mla_sparepart_movements.movement_date) as movement_year,
	month(mla_sparepart_movements.movement_date) as movement_month,
	concat(month(mla_sparepart_movements.movement_date),'.', year(mla_sparepart_movements.movement_date) ) as movement_period
from mla_sparepart_movements
where mla_sparepart_movements.sparepart_id =3
group by mla_sparepart_movements.sparepart_id, mla_sparepart_movements.flow,concat(month(mla_sparepart_movements.movement_date),'.', year(mla_sparepart_movements.movement_date) )
order by concat(month(mla_sparepart_movements.movement_date),'.', year(mla_sparepart_movements.movement_date) ) desc			
	";

    private $movement_year_SQL = "
			
			";

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    /**
     *
     * @param unknown $id
     * @throws \Exception
     */
    public function get($id)
    {
        $id = (int) $id;

        $where = 'id = ' . $id;
        $rowset = $this->tableGateway->select($where);
        $row = $rowset->current();
        if (! $row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /**
     *
     * @param SparepartMovement $input
     */
    public function add(SparepartMovement $input)
    {
        $data = array(
            'movement_date' => $input->movement_date,
            'sparepart_id' => $input->sparepart_id,
            'asset_id' => $input->asset_id,
            'asset_name' => $input->asset_name,

            'flow' => $input->flow,
            'quantity' => $input->quantity,
            'reason' => $input->reason,
            'requester' => $input->requester,

            'comment' => $input->comment,
            'created_on' => date('Y-m-d H:i:s'),

            'wh_id' => $input->wh_id,
            'movement_type' => $input->movement_type,
            'asset_location' => $input->asset_location
        );
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    /**
     *
     * @param SparepartPicture $input
     * @param unknown $id
     */
    public function update(SparepartMovement $input, $id)
    {
        $data = array(
            'movement_date' => $input->movement_date,
            'sparepart_id' => $input->sparepart_id,
            'asset_id' => $input->asset_id,
            'asset_name' => $input->asset_name,

            'flow' => $input->flow,
            'quantity' => $input->quantity,
            'reason' => $input->reason,
            'requester' => $input->requester,
            'comment' => $input->comment,
            'created_on' => date('Y-m-d H:i:s'),
            'asset_location' => $input->asset_location
        );

        $where = 'id = ' . $id;
        $resultSet = $this->tableGateway->update($data, $where);
    }

    public function delete($id)
    {
        $where = 'id = ' . $id;
        $this->tableGateway->delete($where);
    }

    /**
     *
     * @param unknown $id
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function getMovementsOfSparepartByID($id)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = new Sql($adapter);
        $select = $sql->select();

        $where = 'sparepart_id =' . $id;

        $select->from('mla_sparepart_movements');
        $select->where($where);

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        return $results;
    }

    /**
     *
     * @param unknown $id
     * @return number
     */
    public function getTotalInflowOf($id)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = new Sql($adapter);
        $select = $sql->select();

        $where = 'sparepart_id =' . $id . ' AND flow = "IN" ';

        $select->from('mla_sparepart_movements');
        $select->where($where);
        $select->columns(array(
            new \Zend\Db\Sql\Expression('SUM(quantity) as inflow')
        ));

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        if ($results->count() > 0) {
            $r = $results->current();
            return (int) $r['inflow'];
        } else {
            return 0;
        }
    }

    /**
     *
     * @param unknown $id
     * @return number
     */
    public function getTotalOutflowOf($id)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = new Sql($adapter);
        $select = $sql->select();

        $where = 'sparepart_id =' . $id . ' AND flow = "OUT" ';

        $select->from('mla_sparepart_movements');
        $select->where($where);
        $select->columns(array(
            new \Zend\Db\Sql\Expression('SUM(quantity) as outflow')
        ));

        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();

        if ($results->count() > 0) {
            $r = $results->current();
            return (int) $r['outflow'];
        } else {
            return 0;
        }
    }

    /**
     *
     * @param unknown $fromDate
     * @param unknown $toDate
     * @param unknown $flow
     * @param unknown $limit
     * @param unknown $offset
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getSparePartMovements($fromDate, $toDate, $flow, $limit, $offset)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = "
select
*
from
(
select 
	mla_sparepart_movements.*,
	mla_asset.name as asset,
    mla_spareparts.name as sparepart_name,
				 mla_spareparts.tag
    
from
mla_sparepart_movements

left join mla_asset
on mla_asset.id = mla_sparepart_movements.asset_id


left join mla_spareparts
on mla_spareparts.id = mla_sparepart_movements.sparepart_id
)
as mla_sparepart_movements
WHERE 1
		";

        $sql = $sql . " AND mla_sparepart_movements.movement_date >= '" . $fromDate . "'";
        $sql = $sql . " AND mla_sparepart_movements.movement_date <= '" . $toDate . "'";

        if ($flow != 'ALL') {
            $sql = $sql . " AND mla_sparepart_movements.flow = '" . $flow . "'";
        }

        $sql = $sql . " ORDER BY mla_sparepart_movements.movement_date DESC";

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        // echo $sql;

        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     *
     * @param unknown $id
     * @param unknown $fromDate
     * @param unknown $toDate
     * @param unknown $flow
     * @param unknown $limit
     * @param unknown $offset
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getSparePartMovementsByID($id, $fromDate, $toDate, $flow, $limit, $offset)
    {
        $adapter = $this->tableGateway->adapter;

        $sql = "
select
*
from
(
select
	mla_sparepart_movements.*,
	mla_asset.name as asset,
    mla_spareparts.name as sparepart_name,
				 mla_spareparts.tag
	
from
mla_sparepart_movements
	
left join mla_asset
on mla_asset.id = mla_sparepart_movements.asset_id
	
	
left join mla_spareparts
on mla_spareparts.id = mla_sparepart_movements.sparepart_id
)
as mla_sparepart_movements
WHERE 1
		";
        $sql = $sql . " AND mla_sparepart_movements.sparepart_id=" . $id;

        if ($fromDate != null and $toDate != null) {
            $sql = $sql . " AND mla_sparepart_movements.movement_date >= '" . $fromDate . "'";
            $sql = $sql . " AND mla_sparepart_movements.movement_date <= '" . $toDate . "'";
        }

        if ($flow != 'ALL' and $flow != null) {
            $sql = $sql . " AND mla_sparepart_movements.flow = '" . $flow . "'";
        }

        $sql = $sql . " ORDER BY mla_sparepart_movements.movement_date DESC";

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        // echo $sql;

        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     *
     * @param unknown $fromDate
     * @param unknown $toDate
     * @param unknown $flow
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getMovements($fromDate, $toDate, $flow)
    {
        $adapter = $this->tableGateway->adapter;
        if ($flow == null || $flow == 'ALL') {
            $sql = "SELECT *
		FROM mla_sparepart_movements as t1
		left join mla_spareparts as t2 on t1.sparepart_id = t2.id
					
		WHERE t1.movement_date >= '" . $fromDate . "'
		AND t1.movement_date <= '" . $toDate . "'";
        } else {
            $sql = "SELECT *
		FROM mla_sparepart_movements as t1
		left join mla_spareparts as t2 on t1.sparepart_id = t2.id
	
		WHERE t1.movement_date >= '" . $fromDate . "'
		AND t1.movement_date <= '" . $toDate . "'
		AND t1.Flow = '" . $flow . "'";
        }
        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     *
     * @param unknown $fromDate
     * @param unknown $toDate
     * @param unknown $flow
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getMovementSummary($year, $sparepart_id)
    {
        $select = "";
        for ($i = 1; $i <= 12; $i ++) {
            $select = $select . " SUM(CASE WHEN mla_sparepart_movements.movement_period = '" . $i . "." . $year . "' THEN mla_sparepart_movements.total_quantity ELSE 0 END) AS 'p" . $i . $year . "',";
        }

        $sql = "
select

	mla_spareparts.id as sp_id,
" . $select . 
        "   mla_sparepart_movements.flow
					
from mla_spareparts
inner join
(
	select
		mla_sparepart_movements.sparepart_id,
	    sum(mla_sparepart_movements.quantity) as total_quantity,	
	    mla_sparepart_movements.flow,
	    mla_sparepart_movements.quantity,
		year(mla_sparepart_movements.movement_date) as movement_year,
		month(mla_sparepart_movements.movement_date) as movement_month,
		concat(month(mla_sparepart_movements.movement_date),'.', year(mla_sparepart_movements.movement_date) ) as movement_period
	from mla_sparepart_movements
	group by mla_sparepart_movements.sparepart_id, mla_sparepart_movements.flow,concat(month(mla_sparepart_movements.movement_date),'.', year(mla_sparepart_movements.movement_date) )
	order by concat(month(mla_sparepart_movements.movement_date),'.', year(mla_sparepart_movements.movement_date) ) desc
) 
as mla_sparepart_movements
on mla_sparepart_movements.sparepart_id = mla_spareparts.id

where 1
					";

        $sql = $sql . " AND mla_sparepart_movements.sparepart_id=" . $sparepart_id;
        $sql = $sql . " GROUP BY mla_sparepart_movements.flow, mla_sparepart_movements.sparepart_id";
        $sql = $sql . " ORDER BY mla_spareparts.id";
        $sql = $sql . ";";

        // echo $sql;

        $adapter = $this->tableGateway->adapter;
        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }
}