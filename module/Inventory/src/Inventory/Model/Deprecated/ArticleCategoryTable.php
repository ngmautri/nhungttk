<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\ResultSet;
use Inventory\Model\ArticleCategory;

class ArticleCategoryTable
{

    protected $tableGateway;

    protected $sql_GetCategory = "
	
	select
	mla_articles_categories.*,
	ifnull(mla_articles_categories_members.totalMembers,0) as totalMembers,
    mla_articles_categories_1.totalChildren
	from mla_articles_categories
	left join
	(
	select 
		mla_articles_categories_members.article_cat_id,
		count(*) as totalMembers
		from mla_articles_categories_members
		group by mla_articles_categories_members.article_cat_id  
	) 
	as mla_articles_categories_members
	on mla_articles_categories.id =  mla_articles_categories_members.article_cat_id
    left join
    (
		select
		mla_articles_categories.parent_id as article_cat_id,
		count(*) as totalChildren
		from mla_articles_categories
		group by mla_articles_categories.parent_id
	) 
    as mla_articles_categories_1
    on mla_articles_categories_1.article_cat_id = mla_articles_categories.id

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

    public function add(ArticleCategory $input)
    {
        $data = array(
            'name' => $input->name,
            'description' => $input->description,
            'parent_id' => $input->parent_id,
            'path' => $input->path,
            'path_depth' => $input->path_depth,
            'created_on' => date('Y-m-d H:i:s'),
            'created_by' => $input->created_by
        );

        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    public function update(ArticleCategory $input, $id)
    {
        $data = array(
            'name' => $input->name,
            'description' => $input->description,
            'parent_id' => $input->parent_id,
            'path' => $input->path,
            'path_depth' => $input->path_depth
        );

        $where = 'id = ' . $id;
        $this->tableGateway->update($data, $where);
    }

    /**
     *
     * @param unknown $id
     */
    public function delete($id)
    {
        $where = 'id = ' . $id;
        $this->tableGateway->delete($where);
    }

    public function isExits($id)
    {
        $adapter = $this->tableGateway->adapter;

        $where = array(
            'id=?' => $id
        );

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from(array(
            't1' => 'mla_articles_categories'
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

    public function getArticleCategories()
    {
        $adapter = $this->tableGateway->adapter;
        $sql = $this->sql_GetCategory;
        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }

    public function getArticleCategoy($id)
    {
        $adapter = $this->tableGateway->adapter;
        $sql = $this->sql_GetCategory;
        $sql = $sql . " AND mla_articles_categories.id = " . $id;
        $statement = $adapter->query($sql);
        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        if ($resultSet->count() > 0) {
            return $resultSet->current();
        }
        return null;
    }
}