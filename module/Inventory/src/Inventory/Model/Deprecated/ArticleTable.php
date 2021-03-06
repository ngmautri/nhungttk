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
class ArticleTable
{

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

    private $getArticles_SQL_V01 = "
	select
	mla_articles.*,
    mla_users.*,
	(mla_articles.total_inflow-mla_articles.total_outflow) as article_balance,
     mla_articles_pics.id as article_pic_id,
	 mla_articles_pics.filename,
     mla_articles_pics.url,
     mla_articles_pics.folder
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

left join
(
	select
				*
				from 
                (
                select * from
					mla_articles_pics
                    order by mla_articles_pics.uploaded_on desc
                )
                as mla_articles_pics
				group by mla_articles_pics.article_id
)
as mla_articles_pics
on mla_articles_pics.article_id = mla_articles.id
WHERE 1	
			";

    private $getArticles_SQL_V02 = "
select
	mla_articles.*,
    mla_users.*,
	(mla_articles.total_inflow-mla_articles.total_outflow) as article_balance,
     mla_articles_pics.id as article_pic_id,
	 mla_articles_pics.filename,
     mla_articles_pics.url,
     mla_articles_pics.folder
from
(
select 
		mla_articles.*,
		ifnull(article_total_inflow.total_inflow,0) as total_inflow,
		ifnull(article_total_outflow.total_outflow,0) as total_outflow,
        mla_articles_categories_members.article_cat_id,
        mla_articles_categories.name as category_name
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
    
    left join mla_articles_categories_members
    on mla_articles.id = mla_articles_categories_members.article_id
    
    left join mla_articles_categories
    on mla_articles_categories.id = mla_articles_categories_members.article_cat_id
    

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

left join
(
	select
				*
				from 
                (
                select * from
					mla_articles_pics
                    order by mla_articles_pics.uploaded_on desc
                )
                as mla_articles_pics
				group by mla_articles_pics.article_id
)
as mla_articles_pics
on mla_articles_pics.article_id = mla_articles.id
WHERE 1
			
			
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
    public function add(Article $input)
    {
        $data = array(
            'article_tag' => $input->article_tag,
            'name_local' => $input->name_local,
            'name' => $input->name,
            'description' => $input->description,
            'keywords' => $input->keywords,
            'type' => $input->type,

            'unit' => $input->unit,
            'code' => $input->code,
            'barcode' => $input->barcode,
            'created_on' => date('Y-m-d H:i:s'),
            'created_by' => $input->created_by,
            'status' => $input->status,
            'visibility' => $input->visibility,
            'remarks' => $input->remarks
        );

        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    /*
     *
     */
    public function update(Article $input, $id)
    {
        $data = array(
            'article_tag' => $input->article_tag,
            'name_local' => $input->name_local,
            'name' => $input->name,
            'description' => $input->description,
            'keywords' => $input->keywords,
            'type' => $input->type,

            'unit' => $input->unit,
            'code' => $input->code,
            'barcode' => $input->barcode,
            'status' => $input->status,
            'visibility' => $input->visibility,
            'remarks' => $input->remarks
        );

        $where = 'id = ' . $id;
        $this->tableGateway->update($data, $where);
    }

    public function delete($id)
    {
        $where = 'id = ' . $id;
        $this->tableGateway->delete($where);
    }

    public function getArticleByID($id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = $this->getArticles_SQL;
        $sql = $sql . " AND mla_articles.id = " . $id;

        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);

        if ($resultSet->count() == 1) :
            return $resultSet->current();
        else :
            return null;
        endif;
    }

    /**
     *
     * @param unknown $user_id
     * @param unknown $limit
     * @param unknown $offset
     */
    public function getArticles($user_id, $limit, $offset)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = $this->getArticles_SQL;

        if ($user_id > 0) {
            $sql = $sql . " AND mla_articles.department_id
				IN (SELECT department_id from mla_departments_members
				where user_id = " . $user_id . ")
				ORDER BY mla_articles.name ";
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }
        $statement = $adapter->query($sql);

        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     *
     * @param unknown $user_id
     * @param unknown $item_type
     * @param unknown $item_status
     * @param unknown $sort_by
     * @param unknown $limit
     * @param unknown $offset
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getArticles_V01($user_id, $item_type, $item_status, $sort_by, $limit, $offset)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = $this->getArticles_SQL_V01;

        if ($user_id > 0) {
            $sql = $sql . " AND mla_users.department_id
				IN (SELECT department_id from mla_departments_members
				where user_id = " . $user_id . ")
				";
        }

        if ($item_status == "All") :
            $item_status = null;
				endif;

            // Type
        if ($item_type != null) {
            $sql = $sql . " AND  mla_articles.type ='" . $item_type . "'";
        }

        // Status
        if ($item_status != null) {
            $sql = $sql . " AND  mla_articles.status ='" . $item_status . "'";
        }

        if ($sort_by == "item_name") {
            $sql = $sql . " ORDER BY mla_articles.name asc";
        }

        if ($sort_by == "created_date") {
            $sql = $sql . " ORDER BY mla_articles.created_on desc";
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = $sql . ";";

        // echo $sql;

        $statement = $adapter->query($sql);

        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    public function getArticles_V02($department_id, $item_type, $item_status, $sort_by, $limit, $offset)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = $this->getArticles_SQL_V01;

        if ($department_id > 0) {
            $sql = $sql . " AND mla_users.department_id = " . $department_id;
        }

        // Type
        if ($item_type != null) {
            $sql = $sql . " AND  mla_articles.type ='" . $item_type . "'";
        }

        // Status
        if ($item_status != null) {
            $sql = $sql . " AND  mla_articles.status ='" . $item_status . "'";
        }

        if ($sort_by == "item_name") {
            $sql = $sql . " ORDER BY mla_articles.name asc";
        }

        if ($sort_by == "created_date") {
            $sql = $sql . " ORDER BY mla_articles.created_on desc";
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = $sql . ";";

        // echo $sql;

        $statement = $adapter->query($sql);

        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     *
     * @param unknown $cat_id
     * @param unknown $limit
     * @param unknown $offset
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getUncategorizedArticlesOfUser($user_id, $limit, $offset)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = $this->getArticles_SQL_V02;

        if ($user_id > 0) {
            $sql = $sql . " AND mla_users.department_id
				IN (SELECT department_id from mla_departments_members
				where user_id = " . $user_id . ")
				";
        }

        $sql = $sql . "And mla_articles.article_cat_id is null";

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = $sql . ";";

        // echo $sql;

        $statement = $adapter->query($sql);

        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    public function getArticlesOfCategory($cat_id, $limit, $offset)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = $this->getArticles_SQL_V02;

        if ($cat_id > 0) {
            $sql = $sql . " AND mla_articles.article_cat_id = " . $cat_id;
        }

        if ($limit > 0) {
            $sql = $sql . " LIMIT " . $limit;
        }

        if ($offset > 0) {
            $sql = $sql . " OFFSET " . $offset;
        }

        $sql = $sql . ";";

        // echo $sql;

        $statement = $adapter->query($sql);

        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     *
     * @param unknown $limit
     * @param unknown $offset
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function getLimitArticles($limit, $offset)
    {
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
    public function getLimittedArticlesOf($id, $limit, $offset)
    {
        $sql = "
		select T1.*, T2.department_id from mla_articles as T1
left join mla_departments_members as T2
on T2.user_id = T1.created_by
Where T1.created_by = " . $id . " limit " . $limit . ' offset ' . $offset;

        $adapter = $this->tableGateway->adapter;
        $statement = $adapter->query($sql);

        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    /**
     *
     * @param
     *            unknown User $id
     */
    public function getArticlesOfMyDepartment($id)
    {
        $sql = "
		SELECT * FROM
		(select T1.*, T2.department_id from mla_articles as T1
				left join mla_departments_members as T2
				on T2.user_id = T1.created_by) AS TT1
				where TT1.department_id IN (SELECT department_id from mla_departments_members
						where user_id = " . $id . ")";

        $adapter = $this->tableGateway->adapter;
        $statement = $adapter->query($sql);

        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }
}