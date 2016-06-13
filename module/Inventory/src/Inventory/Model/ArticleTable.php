<?php

namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Inventory\Model\Article;

/**
 * 
 * @author nmt
 *
 */
class ArticleTable {
	
	protected $tableGateway;
	private $getArticles_SQL = "
			select
*
from 
(
select
	*,
	(mla_articles.total_inflow-mla_articles.total_outflow) as article_balance
from
(
	select 
		mla_articles.*,
		ifnull(article_total_inflow.total_inflow,0) as total_inflow,
		ifnull(article_total_outflow.total_outflow,0) as total_outflow
	from mla_articles

	/*total infow*/
	left join
	(
		select 
		mla_articles_movements.article_id,
		ifnull(sum(mla_articles_movements.quantity),0) as total_inflow
		from mla_articles_movements
		where mla_articles_movements.flow = 'IN'
		group by article_id
	)
	as article_total_inflow
	on article_total_inflow.article_id = mla_articles.id

	/*total outflow*/
	left join
	(
		select 
		mla_articles_movements.article_id,
		ifnull(sum(mla_articles_movements.quantity),0)as total_outflow
		from mla_articles_movements
		where mla_articles_movements.flow = 'OUT'
		group by article_id
	)
	as article_total_outflow
	on article_total_outflow.article_id = mla_articles.id
)
as mla_articles

join
(
	
    /**USER-DEPARTMENT beginns*/
    select 
        mla_users.title, 
        mla_users.firstname, 
        mla_users.lastname, 
        mla_departments_members_1.*
    from mla_users
    join 
	(	select 
			mla_departments_members.department_id,
            mla_departments_members.user_id,
            mla_departments.name as department_name,
            mla_departments.status as department_status
		from mla_departments_members
		join mla_departments on mla_departments_members.department_id = mla_departments.id
	) as mla_departments_members_1 
    on mla_users.id = mla_departments_members_1.user_id
    /**USER-DEPARTMENT ends*/
)
as mla_users
on mla_users.user_id = mla_articles.created_by
)
as mla_articles
WHERE 1	
			";
	
	
	
	public function __construct(TableGateway $tableGateway) {
		$this->tableGateway = $tableGateway;
	}
	public function fetchAll() {
		$resultSet = $this->tableGateway->select ();
		return $resultSet;
	}
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
	
	/*
	 * public $id;
	 * public $name;
	 * public $description;
	 * public $keywords;
	 *
	 * public $type;
	 * public $code;
	 * public $barcode;
	 *
	 * public $created_on;
	 * public $created_by;
	 * public $status;
	 * public $visibility;
	 * public $remarks;
	 */
	public function add(Article $input) {
		
		$data = array (
				'article_tag' => $input->article_tag,
				'name_local' => $input->name_local,
				'name' => $input->name,
				'description' => $input->description,
				'keywords' => $input->keywords,
				'type' => $input->type,
				
				'unit' => $input->unit,
				'code' => $input->code,				
				'barcode' => $input->barcode,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'created_by' => $input->created_by,
				'status' => $input->status,
				'visibility' => $input->visibility,
				'remarks' => $input->remarks,
			);
		
		$this->tableGateway->insert ( $data );
		return $this->tableGateway->lastInsertValue;
	}
	
	/*
	 * 
	 */
	public function update(Article $input, $id) {
		$data = array (
				'article_tag' => $input->article_tag,
				'name_local' => $input->name_local,
				'name' => $input->name,
				'description' => $input->description,
				'keywords' => $input->keywords,
				'type' => $input->type,
				
				'unit' => $input->unit,
				'code' => $input->code,				
				'barcode' => $input->barcode,
				'created_on' => date ( 'Y-m-d H:i:s' ),
				'status' => $input->status,
				'visibility' => $input->visibility,
				'remarks' => $input->remarks,
		);
		
		$where = 'id = ' . $id;
		$this->tableGateway->update ( $data, $where );
	}
	public function delete($id) {
		$where = 'id = ' . $id;
		$this->tableGateway->delete ( $where );
	}
	
	
	public function getArticleByID($id){
		
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getArticles_SQL;
		$sql = $sql. " AND mla_articles.id = " . $id;
		
		$statement = $adapter->query($sql);	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		
		if($resultSet->count() == 1):
			return $resultSet->current();
		else:
			return null;
		endif;
	}
	
	/**
	 * 
	 * @param unknown $user_id
	 * @param unknown $limit
	 * @param unknown $offset
	 */
	public function getArticles($user_id,$limit, $offset){
	
		$adapter = $this->tableGateway->adapter;
		$sql = $this->getArticles_SQL;
	
		if ($user_id > 0) {
			$sql = $sql. " AND mla_articles.department_id
				IN (SELECT department_id from mla_departments_members
				where user_id = ".$user_id.")
				ORDER BY mla_articles.name ";
		}
	
		if ($limit > 0) {
			$sql = $sql. " LIMIT " . $limit;
		}
	
		if ($offset > 0) {
			$sql = $sql. " OFFSET " . $offset;
		}
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	

	public function getLimitArticles($limit,$offset){
		$adapter = $this->tableGateway->adapter;
		
		$sql = new Sql($adapter);
		$select = $sql->select();
		
				
		$select->from('mla_articles');
		$select->limit($limit)->offset($offset);
		
		$statement = $sql->prepareStatementForSqlObject($select);
		$results = $statement->execute();
		
		// array
		return $results;
	}
	
	/**
	 * 
	 * @param unknown $id
	 */		
	public function getArticlesOf($id)
	{
	
		$sql = "
		select T1.*, T2.department_id from mla_articles as T1
left join mla_departments_members as T2
on T2.user_id = T1.created_by
Where T1.created_by = " . $id;
	
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $id
	 */
	public function getLimittedArticlesOf($id,$limit,$offset)
	{
	
		$sql = "
		select T1.*, T2.department_id from mla_articles as T1
left join mla_departments_members as T2
on T2.user_id = T1.created_by
Where T1.created_by = " . $id . 
" limit " . $limit . ' offset '. $offset;	
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown User $id
	 */
	public function getArticlesOfMyDepartment($id)
	{
	
		$sql ="
		SELECT * FROM
		(select T1.*, T2.department_id from mla_articles as T1
				left join mla_departments_members as T2
				on T2.user_id = T1.created_by) AS TT1
				where TT1.department_id IN (SELECT department_id from mla_departments_members
						where user_id = " . $id .")";
		
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown User $id
	 */
	public function getLimitedArticlesOfMyDepartment($id,$limit,$offset)
	{
	
		$sql ="
		SELECT * FROM
		(select T1.*, T2.department_id from mla_articles as T1
				left join mla_departments_members as T2
				on T2.user_id = T1.created_by) AS TT1
				where TT1.department_id IN (select department_id from mla_departments_members
						where user_id = " . $id .") limit " . $limit . ' offset '. $offset; 
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $id
	 */
	public function getAllWithLastDO()
	{
	
		$sql = "
select 
	mla_articles.*,
	mla_delivery_items_1_1.delivery_id,
    mla_delivery_items_1_1.pr_item_id,
	mla_delivery_items_1_1.delivered_quantity,
	mla_delivery_items_1_1.price,
    mla_delivery_items_1_1.currency,
    mla_delivery_items_1_1.vendor_id,
	mla_delivery_items_1_1.vendor_name,
	mla_delivery_items_1_1.created_on  as last_do_date

from mla_articles


left join

(
	select 
	mla_delivery_items_1.*,
    mla_delivery_items_1_1_1.*
    
	from (
		select
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.article_id, '+++',mla_delivery_items.created_on) as article_do, 
			mla_delivery_items.*
		from mla_delivery_items
		join mla_purchase_request_items 
		on mla_purchase_request_items.id =  mla_delivery_items.pr_item_id
	) as mla_delivery_items_1

	JOIN
		(   
	 /*Last DO Item*/
	select 
	  mla_delivery_items_1_1.vendor_name,
      concat(mla_delivery_items_1_1.article_id, '+++',mla_delivery_items_1_1.last_do_item_created_on) as last_article_do

	from 
	 
	 (select MAX(mla_delivery_items_1.do_item_created_on) AS last_do_item_created_on, 
	 mla_delivery_items_1.do_item_id,
	 mla_delivery_items_1.article_id,		
	 mla_vendors.name as vendor_name

		from  

			(select 
				mla_delivery_items.id as do_item_id,
				mla_delivery_items.created_on as do_item_created_on, 
				mla_delivery_items.pr_item_id, 
				mla_delivery_items.vendor_id, 
				mla_delivery_items.price,
				mla_delivery_items.currency,
				mla_delivery_items.created_by as do_item_created_by_user_id,
				mla_purchase_request_items.* 
			from mla_delivery_items 
			join mla_purchase_request_items
			on mla_purchase_request_items.id = mla_delivery_items.pr_item_id) as mla_delivery_items_1 /* DELIVERY - PR*/

		join mla_vendors 
		on mla_vendors.id = mla_delivery_items_1.vendor_id /* DELIVERY - PR - VENDOR*/
		
	group by mla_delivery_items_1.article_id) as mla_delivery_items_1_1
			
			) as mla_delivery_items_1_1_1 /* Last DO Article */
		
	ON mla_delivery_items_1_1_1.last_article_do = mla_delivery_items_1.article_do ) AS mla_delivery_items_1_1
    
ON mla_delivery_items_1_1.article_id = mla_articles.id
";
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
	
	/**
	 *
	 * @param unknown $id
	 */
	public function getAllWithLastDOWithLimit($limit, $offset)
	{
	
		$sql = "
select 
	mla_articles.*,
	mla_delivery_items_1_1.delivery_id,
    mla_delivery_items_1_1.pr_item_id,
	mla_delivery_items_1_1.delivered_quantity,
	mla_delivery_items_1_1.price,
    mla_delivery_items_1_1.currency,
    mla_delivery_items_1_1.vendor_id,
	mla_delivery_items_1_1.vendor_name,
	mla_delivery_items_1_1.created_on  as last_do_date

from mla_articles


left join

(
	select 
	mla_delivery_items_1.*,
    mla_delivery_items_1_1_1.*
    
	from (
		select
			mla_purchase_request_items.article_id,
			concat(mla_purchase_request_items.article_id, '+++',mla_delivery_items.created_on) as article_do, 
			mla_delivery_items.*
		from mla_delivery_items
		join mla_purchase_request_items 
		on mla_purchase_request_items.id =  mla_delivery_items.pr_item_id
	) as mla_delivery_items_1

	JOIN
		(   
	 /*Last DO Item*/
	select 
	  mla_delivery_items_1_1.vendor_name,
      concat(mla_delivery_items_1_1.article_id, '+++',mla_delivery_items_1_1.last_do_item_created_on) as last_article_do

	from 
	 
	 (select MAX(mla_delivery_items_1.do_item_created_on) AS last_do_item_created_on, 
	 mla_delivery_items_1.do_item_id,
	 mla_delivery_items_1.article_id,		
	 mla_vendors.name as vendor_name

		from  

			(select 
				mla_delivery_items.id as do_item_id,
				mla_delivery_items.created_on as do_item_created_on, 
				mla_delivery_items.pr_item_id, 
				mla_delivery_items.vendor_id, 
				mla_delivery_items.price,
				mla_delivery_items.currency,
				mla_delivery_items.created_by as do_item_created_by_user_id,
				mla_purchase_request_items.* 
			from mla_delivery_items 
			join mla_purchase_request_items
			on mla_purchase_request_items.id = mla_delivery_items.pr_item_id) as mla_delivery_items_1 /* DELIVERY - PR*/

		join mla_vendors 
		on mla_vendors.id = mla_delivery_items_1.vendor_id /* DELIVERY - PR - VENDOR*/
		
	group by mla_delivery_items_1.article_id) as mla_delivery_items_1_1
			
			) as mla_delivery_items_1_1_1 /* Last DO Article */
		
	ON mla_delivery_items_1_1_1.last_article_do = mla_delivery_items_1.article_do ) AS mla_delivery_items_1_1
    
ON mla_delivery_items_1_1.article_id = mla_articles.id
limit " . $limit . ' offset '. $offset;	
				
	
		$adapter = $this->tableGateway->adapter;
		$statement = $adapter->query($sql);
	
		$result = $statement->execute();
	
		$resultSet = new \Zend\Db\ResultSet\ResultSet();
		$resultSet->initialize($result);
		return $resultSet;
	}
}