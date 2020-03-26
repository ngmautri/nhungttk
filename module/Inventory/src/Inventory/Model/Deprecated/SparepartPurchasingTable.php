<?php
namespace Inventory\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Inventory\Model\SparepartPurchasing;

class SparepartPurchasingTable
{

    protected $tableGateway;

    /**
     *
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     */
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

    /*
     * public $id;
     * public $article_id;
     * public $vendor_id;
     *
     * public $vendor_article_code;
     * public $vendor_unit;
     * public $vendor_unit_price;
     * public $currency;
     * public $price_valid_from;
     * public $is_preferred;
     *
     * public $created_on;
     * public $created_by;
     */
    public function add(SparepartPurchasing $input)
    {
        $data = array(
            'article_id' => $input->article_id,
            'vendor_id' => $input->vendor_id,

            'vendor_article_code' => $input->vendor_article_code,
            'vendor_unit' => $input->vendor_unit,
            'vendor_unit_price' => $input->vendor_unit_price,
            'currency' => $input->currency,
            'price_valid_from' => $input->price_valid_from,
            'is_preferred' => $input->is_preferred,
            'created_on' => date('Y-m-d H:i:s'),
            'created_by' => $input->created_by
        );
        $this->tableGateway->insert($data);
        return $this->tableGateway->lastInsertValue;
    }

    /**
     *
     * @param SparepartPicture $input
     * @param unknown $id
     */
    public function update(SparepartPurchasing $input, $id)
    {
        $data = array(
            'article_id' => $input->article_id,
            'vendor_id' => $input->vendor_id,

            'vendor_article_code' => $input->vendor_article_code,
            'vendor_unit' => $input->vendor_unit,
            'vendor_unit_price' => $input->vendor_unit_price,
            'currency' => $input->currency,
            'price_valid_from' => $input->price_valid_from,
            'is_preferred' => $input->is_preferred,
            'created_on' => date('Y-m-d H:i:s'),
            'created_by' => $input->created_by
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

    /**
     *
     * @param
     *            unknown User $id
     */
    public function getPurchasingDataOf($id)
    {
        $sql = "select 
	mla_spareparts_purchasing.*,
	mla_vendors.name as vendor_name,
	mla_spareparts.name as article_name,
    mla_spareparts.code as article_code
    
from mla_spareparts_purchasing
join mla_vendors
on mla_vendors.id = mla_spareparts_purchasing.vendor_id
join mla_spareparts
on mla_spareparts.id = mla_spareparts_purchasing.article_id Where 1";

        $sql = $sql . " AND mla_spareparts_purchasing.article_id=" . $id;
        $sql = $sql . " Order by mla_spareparts_purchasing.price_valid_from DESC";

        $adapter = $this->tableGateway->adapter;
        $statement = $adapter->query($sql);

        $result = $statement->execute();

        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($result);
        return $resultSet;
    }
}