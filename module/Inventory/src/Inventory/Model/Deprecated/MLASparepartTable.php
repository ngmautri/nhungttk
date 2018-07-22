<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\ParameterContainer;
use Inventory\Model\MLASparepart;

/**
 *
 * @author nmt
 *        
 */
class MLASparepartTable {
	protected $tableGateway;
	private $getPendingPRItems_SQL = "

	select
	mla_purchase_request_items.*,
    mla_spareparts.tag,
    mla_spareparts.name as sp_name,
    
    
    mla_purchase_requests.id as pr_id,
    mla_purchase_requests.pr_number,
	if ((mla_purchase_request_items.quantity - (ifnull(mla_delivery_items_workflows.confirmed_quantity,0)+ ifnull(mla_pr_item_self_confirmation.self_confirmed_quantity,0)))>=0
		,(mla_purchase_request_items.quantity - (ifnull(mla_delivery_items_workflows.confirmed_quantity,0)+ ifnull(mla_pr_item_self_confirmation.self_confirmed_quantity,0)))
		,0) as confirmed_balance,
        
	ifnull(mla_delivery_items_notified.unconfimed_quantity,0) as unconfirmed_quantity,
    ifnull( mla_delivery_items.total_received_quantity,0) as total_received_quantity,
    
    ifnull(mla_pr_item_self_confirmation.self_confirmed_quantity,0) as self_confirmed_quantity,
    ifnull( mla_pr_item_self_confirmation.self_rejected_quantity,0) as self_rejected_quantity
    
    
	from mla_purchase_request_items
    
    left join mla_purchase_requests
    on mla_purchase_requests.id = mla_purchase_request_items.purchase_request_id

    
	left join mla_spareparts
	on mla_spareparts.id = mla_purchase_request_items.sparepart_id

	/* total confirmed and rejected DN */
	left join
	(
		select
		mla_delivery_items_workflows.pr_item_id,
		sum(mla_delivery_items_workflows.confirmed_quantity) as confirmed_quantity,
		sum(mla_delivery_items_workflows.rejected_quantity) as rejected_quantity
		from mla_delivery_items_workflows
		group by mla_delivery_items_workflows.pr_item_id
	)
	as mla_delivery_items_workflows
	on mla_delivery_items_workflows.pr_item_id = mla_purchase_request_items.id
    
 	/* total self-confirmed and self-rejected DN */
	left join
	(
		select
		mla_pr_item_self_confirmation.pr_item_id,
		sum(mla_pr_item_self_confirmation.confirmed_quantity) as self_confirmed_quantity,
		sum(mla_pr_item_self_confirmation.rejected_quantity) as self_rejected_quantity
		from mla_pr_item_self_confirmation
		group by mla_pr_item_self_confirmation.pr_item_id
	)
	as mla_pr_item_self_confirmation
	on mla_pr_item_self_confirmation.pr_item_id = mla_purchase_request_items.id
     
    /* total notified /unconfirmed DN */
	left join
	(
		select
			mla_delivery_items.*,
			sum(mla_delivery_items.delivered_quantity) as unconfimed_quantity
		from
		(
			select 
				mla_delivery_items.*,
				mla_delivery_items_workflows.status as dn_last_status,
				mla_delivery_items_workflows.updated_on as dn_last_status_on,
				mla_delivery_items_workflows.updated_by as dn_last_status_by
			from mla_delivery_items
			left join mla_delivery_items_workflows
			on mla_delivery_items_workflows.id = mla_delivery_items.last_workflow_id
			)
		as mla_delivery_items
		where mla_delivery_items.dn_last_status = 'Notified'
		group by mla_delivery_items.pr_item_id
	)
	as mla_delivery_items_notified
	on mla_delivery_items_notified.pr_item_id = mla_purchase_request_items.id

	/* total_received_quantity*/
	left join
	(
		select
		mla_delivery_items.po_item_id,
		mla_delivery_items.pr_item_id,
		sum(mla_delivery_items.delivered_quantity) as total_received_quantity
		from mla_delivery_items
		group by mla_delivery_items.pr_item_id
	) 
	as mla_delivery_items
	on mla_delivery_items.pr_item_id = mla_purchase_request_items.id 

	Where 1

	AND if ((mla_purchase_request_items.quantity - (ifnull(mla_delivery_items_workflows.confirmed_quantity,0)+ ifnull(mla_pr_item_self_confirmation.self_confirmed_quantity,0)))>=0
		,(mla_purchase_request_items.quantity - (ifnull(mla_delivery_items_workflows.confirmed_quantity,0)+ ifnull(mla_pr_item_self_confirmation.self_confirmed_quantity,0)))
		,0) >0
				";
	private $getSP_SQl = "
SELECT
 *
FROM mla_spareparts
LEFT JOIN 
(
SELECT
	mla_sparepart_movements.sparepart_id,
    IFNULL(SUM(CASE WHEN mla_sparepart_movements.flow = 'IN' THEN mla_sparepart_movements.quantity ELSE 0 END),0) AS total_inflow,
    IFNULL(SUM(CASE WHEN mla_sparepart_movements.flow = 'OUT' THEN mla_sparepart_movements.quantity ELSE 0 END),0) AS total_outflow
FROM mla_sparepart_movements
GROUP BY mla_sparepart_movements.sparepart_id
)
AS mla_sparepart_movements
ON mla_sparepart_movements.sparepart_id = mla_spareparts.id
WHERE 1";
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	
	/**
	 *
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function fetchAll() {
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		$select->from ( 'mla_spareparts' );
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$results = $statement->execute ();
		
		// array
		return $results;
	}
	public function getSpareparts() {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "select lt1.*, tIN.totalINFLOW, tOUT.totalOUTFLOW from mla_spareparts as lt1 left join
(select  t1.id, SUM(t2.quantity) AS totalINFLOW from mla_spareparts as t1 LEFT JOIN mla_sparepart_movements as t2
on t2.sparepart_id = t1.id where t2.flow = 'IN' GROUP BY t2.sparepart_id) as tIN on tIN.id = lt1.id

left join
(select t3.id, SUM(t4.quantity) as totalOUTFLOW FROM mla_spareparts as t3 LEFT JOIN mla_sparepart_movements as t4
on t4.sparepart_id = t3.id where t4.flow = 'OUT' group by t4.sparepart_id) as tOUT on tOUT.id = lt1.id
		";
		
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	public function getLimitedSpareparts($limit, $offset) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = "select lt1.*, tIN.totalINFLOW, tOUT.totalOUTFLOW from mla_spareparts as lt1 left join
(select  t1.id, SUM(t2.quantity) AS totalINFLOW from mla_spareparts as t1 LEFT JOIN mla_sparepart_movements as t2
on t2.sparepart_id = t1.id where t2.flow = 'IN' GROUP BY t2.sparepart_id) as tIN on tIN.id = lt1.id
	
left join
(select t3.id, SUM(t4.quantity) as totalOUTFLOW FROM mla_spareparts as t3 LEFT JOIN mla_sparepart_movements as t4
on t4.sparepart_id = t3.id where t4.flow = 'OUT' group by t4.sparepart_id) as tOUT on tOUT.id = lt1.id
		limit " . $limit . ' offset ' . $offset;
		
		$statement = $adapter->query ( $sql );
		
		// $container = new ParameterContainer();
		// $container->offsetSet('limit', $limit, $container::TYPE_INTEGER);
		// $container->offsetSet('offset', $offset, $container::TYPE_INTEGER);
		
		// $parameters = array((int)$limit,(int)$offset);
		
		// bug with quoting LIMIT and OFFSET
		$result = $statement->execute ();
		
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $id        	
	 * @throws \Exception
	 */
	public function get($id) {
		$id = ( int ) $id;
		
		$rowset = $this->tableGateway->select ( array (
				'id' => $id 
		) );
		$row = $rowset->current ();
		if (! $row) {
			throw new \Exception ( "Could not find row $id" );
		}
		return $row;
	}
	
	/**
	 *
	 * @param unknown $limit        	
	 * @param unknown $offset        	
	 * @return \Zend\Db\Adapter\Driver\ResultInterface
	 */
	public function getLimitSpareParts($limit, $offset) {
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql ( $adapter );
		$select = $sql->select ();
		
		$select->from ( 'mla_spareparts' );
		$select->limit ( $limit )->offset ( $offset );
		
		$statement = $sql->prepareStatementForSqlObject ( $select );
		$results = $statement->execute ();
		
		// array
		return $results;
	}
	
	/**
	 *
	 * @param MLASparepart $input        	
	 */
	public function add(MLASparepart $input) {
		$data = array (
				'name' => $input->name,
				'name_local' => $input->name_local,
				'description' => $input->description,
				'code' => $input->code,
				'tag' => $input->tag,
				'location' => $input->location,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ) 
		);
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/**
	 * 
	 * @param MLAAsset $input
	 * @param unknown $id
	 */
	public function update(MLASparepart $input, $id) {
		
		$data = array (
				'name' => $input->name,
				'name_local' => $input->name_local,
				'description' => $input->description,				
				'code' => $input->code,				
				'tag' => $input->tag,
				'location' => $input->location,
				'comment' => $input->comment,
				'created_on' => date ( 'Y-m-d H:i:s' ) 
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update( $data,$where);
	}
	
	
	/**
	 * 
	 * @param unknown $id
	 */
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete($where);
	}
	
	
	public function isTagExits($tag)
	{
		$adapter = $this->tableGateway->adapter;
	
		$where = array(
				'tag=?'		=>$tag,
		);
	
		$sql = new Sql($adapter);
		$select = $sql->select();
	
		$select->from(array('t1'=>'mla_spareparts'));
		$select->where($where);
	
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
	
		if($results->count()>0){
			return true;
		}else{
			return false;
		}
	}
	
	
	/**
	 * 
	 * @param unknown $sp_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getPendingPRItems($sp_id){
	
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getPendingPRItems_SQL;
		
		$sql = $sql . " AND mla_spareparts.id = ".$sp_id;
		
		//echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	
	}
	
	/**
	 *
	 * @param unknown $sp_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getSPConsumptionByAsset($asset_id){
	
		$adapter = $this->tableGateway->adapter;
		$sql1 = "
select
	mla_sparepart_movements.*,
    mla_spareparts.name as sp_name,
    mla_spareparts.tag as sp_tag,
	mla_asset.name as a_name
				
    
from mla_sparepart_movements

left join mla_asset
on mla_asset.id = mla_sparepart_movements.asset_id

left join mla_spareparts
on mla_spareparts.id = mla_sparepart_movements.sparepart_id

where 1
and mla_sparepart_movements.flow = 'OUT'				
		";
		$sql1=$sql1. " AND mla_asset.id = ". $asset_id;
		
	
		$sql2 = "
select
mla_sparepart_movements.*,
   mla_spareparts.name as sp_name,
    mla_spareparts.tag as sp_tag,
	mla_asset.name as a_name
				
 

from mla_sparepart_movements
left join mla_asset
on mla_asset.tag=mla_sparepart_movements.asset_name

left join mla_spareparts
on mla_spareparts.id = mla_sparepart_movements.sparepart_id

where 1
and mla_sparepart_movements.asset_id is null
and mla_sparepart_movements.flow = 'OUT'		
		";
		$sql2=$sql2. " AND mla_asset.id = ". $asset_id;
		
		
		$sql = $sql1 . " UNION ". $sql2 . ";";
	
		//echo ($sql);
	
		$statement = $adapter->query ( $sql );
		$result = $statement->execute ();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet ();
		$resultSet->initialize ( $result );
		return $resultSet;
	
	}
	
	/**
	 *
	 * @param unknown $sp_id
	 * @return \Zend\Db\ResultSet\ResultSet
	 */
	public function getSP($sp_id){
	
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getSP_SQl;
	
		if($sp_id >0) {
			$sql = $sql . " AND mla_spareparts.id = ".$sp_id;
	
			$statement = $adapter->query ( $sql );
			$result = $statement->execute ();
			
			$resultSet = new \Zend\Db\ResultSet\ResultSet ();
			$resultSet->initialize ( $result );
			
			if($resultSet->count()>0){
				return $resultSet->current();
			}else{
				return null;
			}
		}
				
		return null;
	}
}