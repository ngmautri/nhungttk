<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Inventory\Model\ArticlePicture;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

class ArticlePictureTable
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
     * @param SparepartPicture $input
     * @return number
     */
    public function add(ArticlePicture $input)
    {
        $data = array(
            'article_id' => $input->article_id,
            'url' => $input->url,
            'filetype' => $input->filetype,
            'size' => $input->size,
            'visibility' => $input->visibility,
            'comments' => $input->comments,
            'uploaded_on' => date('Y-m-d H:i:s'),
            'filename' => $input->filename,
            'folder' => $input->folder,
            'checksum' => $input->checksum
        );
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    /**
     *
     * @param SparepartPicture $input
     * @param unknown $id
     */
    public function update(SparepartPicture $input, $id)
    {
        $data = array(
            'article_id' => $input->article_id,
            'url' => $input->url,
            'filetype' => $input->filetype,
            'size' => $input->size,
            'visibility' => $input->visibility,
            'comments' => $input->comments,
            'uploaded_on' => date('Y-m-d H:i:s'),
            'filename' => $input->filename,
            'folder' => $input->folder,
            'checksum' => $input->checksum
        );

        $where = 'id = ' . $id;
        $this->tableGateway->update($data, $where);
    }

    /**
     *
     * @param unknown $id
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function getArticlePicturesById($id)
    {
        $id = (int) $id;

        $where = 'article_id = ' . $id;
        $rowset = $this->tableGateway->select($where);
        return $rowset;
    }

    public function delete($id)
    {
        $where = 'id = ' . $id;
        $this->tableGateway->delete($where);
    }

    public function isChecksumExits($id, $checksum)
    {
        $adapter = $this->tableGateway->adapter;

        $where = array(
            'article_id=?' => $id,
            'checksum=?' => $checksum
        );

        $sql = new Sql($adapter);
        $select = $sql->select();

        $select->from(array(
            't1' => 'mla_articles_pics'
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