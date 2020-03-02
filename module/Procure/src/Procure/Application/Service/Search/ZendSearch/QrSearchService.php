<?php
namespace Procure\Application\Service\Search\ZendSearch;

use Application\Service\AbstractService;
use Doctrine\ORM\EntityManager;
use Procure\Domain\Service\Search\QrSearchInterface;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\Boolean;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrSearchService extends AbstractService implements QrSearchInterface
{

    const ITEM_INDEX = "/data/procure/indexes/po";

    protected $doctrineEM;

    public function updateIndex($entityId, $isNew = true, $optimized = false)
    {}

    public function createDoc($doc, $isNew = true)
    {}

    /**
     *
     * @return string
     */
    public function createIndex()
    {

        // take long time
        set_time_limit(1500);
        try {
            $index = Lucene::create(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());

            $query = 'SELECT e, i, po FROM Application\Entity\NmtProcurePoRow e JOIN e.item i JOIN e.po po Where 1=?1';
            $records = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => 1
            ))
                ->getResult();

            // $records = $this->doctrineEM->getRepository ( 'Application\Entity\NmtInventoryItem' )->findAll ();

            if (count($records) > 0) {

                foreach ($records as $row) {
                    $this->_addDocument($index, $row);
                }
                $index->optimize();
                $log1 = 'PO indexes is created successfully!<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();
                return $log1;
            } else {
                $log1 = 'Nothing for indexing!';
                return $log1;
            }
        } catch (Exception $e) {
            $log1 = $e->getMessage();
            return $log1;
        }
    }

    /**
     *
     * @param array $po_rows
     * @param boolean $optimized
     * @return string
     */
    public function indexingPoRows($po_rows, $optimized = FALSE)
    {
        if (count($po_rows) == 0) {
            return 'No po rows found';
        }

        // take long time
        set_time_limit(1500);

        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());

            foreach ($po_rows as $row) {

                /** @var \Application\Entity\NmtProcurePoRow $row ; */
                if (! $row instanceof \Application\Entity\NmtProcurePoRow) {
                    continue;
                }
                $this->_addDocument($index, $row);
            }

            if ($optimized == TRUE) {
                $index->optimize();
            }

            return 'Document has been added successfully !<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $index
     * @param \Application\Entity\NmtProcurePoRow $row
     */
    private function _addDocument(\ZendSearch\Lucene\SearchIndexInterface $index, \Application\Entity\NmtProcurePoRow $row)
    {
        if ($index == null || ! $row instanceof \Application\Entity\NmtProcurePoRow) {
            return;
        }

        if ($row->getPo() == null) {
            return;
        }

        $doc = new Document();

        $doc->addField(Field::UnIndexed('po_row_id', $row->getId()));
        $doc->addField(Field::UnIndexed('token', $row->getToken()));

        $doc->addField(Field::Keyword('row_token_keyword', $row->getToken() . "__" . $row->getId()));
        $doc->addField(Field::Keyword('row_identifer_keyword', $row->getRowIdentifer()));

        $doc->addField(Field::UnIndexed('row_quantity', $row->getQuantity()));
        $doc->addField(Field::UnIndexed('row_conversion_factor', $row->getConversionFactor()));

        $doc->addField(Field::UnIndexed('row_unit', $row->getUnit()));
        $doc->addField(Field::UnIndexed('row_unit_price', $row->getUnitPrice()));

        $doc->addField(Field::text('row_remarks', $row->getRemarks()));
        $doc->addField(Field::text('row_name', $row->getVendorItemCode()));

        $doc->addField(Field::UnIndexed('po_id', $row->getPo()
            ->getId()));
        $doc->addField(Field::UnIndexed('po_token', $row->getPo()
            ->getToken()));

        $doc->addField(Field::text('po_number', $row->getPo()
            ->getContractNo()));

        $doc->addField(Field::Keyword('po_sys_number', $row->getPo()
            ->getSysNumber()));

        $doc->addField(Field::Keyword('po_doc_status', $row->getPo()
            ->getDocStatus()));

        if ($row->getPo()->getVendor() != null) {
            $doc->addField(Field::Keyword('vendor_id_keyword', 'vendor_id_keyword=' . $row->getPo()
                ->getVendor()
                ->getId()));
            $doc->addField(Field::UnIndexed('vendor_id', $row->getPo()
                ->getVendor()
                ->getId()));

            $doc->addField(Field::text('vendor_name', $row->getPo()
                ->getVendor()
                ->getVendorName()));
        } else {
            $doc->addField(Field::Keyword('vendor_id_keyword', "vendor_id_keyword="));
            $doc->addField(Field::UnIndexed('vendor_name', ""));
            $doc->addField(Field::UnIndexed('vendor_id', ""));
        }

        if ($row->getItem() !== null) {
            $doc->addField(Field::UnIndexed('item_id', $row->getItem()
                ->getId()));
            $doc->addField(Field::UnIndexed('item_token', $row->getItem()
                ->getToken()));
            $doc->addField(Field::UnIndexed('item_checksum', $row->getItem()
                ->getChecksum()));

            $doc->addField(Field::Keyword('manufacturer_model_key', $row->getItem()
                ->getManufacturerModel()));
            $doc->addField(Field::Keyword('manufacturer_serial_key', $row->getItem()
                ->getManufacturerSerial()));
            $doc->addField(Field::Keyword('manufacturer_code_key', $row->getItem()
                ->getManufacturerCode()));

            $doc->addField(Field::Keyword('sp_label_key', $row->getItem()
                ->getSparepartLabel()));
            $doc->addField(Field::Keyword('asset_label_key', $row->getItem()
                ->getAssetLabel()));
            $doc->addField(Field::Keyword('item_sku_key', $row->getItem()
                ->getItemSku()));
            $doc->addField(Field::Keyword('item_sys_number', $row->getItem()
                ->getSysNumber()));
            $doc->addField(Field::text('item_name', $row->getItem()
                ->getItemName()));
            $doc->addField(Field::text('manufacturer', $row->getItem()
                ->getManufacturer()));
            $doc->addField(Field::text('manufacturer_model', $row->getItem()
                ->getManufacturerModel()));
            $doc->addField(Field::text('manufacturer_serial', $row->getItem()
                ->getManufacturerSerial()));
            $doc->addField(Field::text('manufacturer_code', $row->getItem()
                ->getManufacturerCode()));
            $doc->addField(Field::text('item_keywords', $row->getItem()
                ->getKeywords()));
            $doc->addField(Field::text('asset_label', $row->getItem()
                ->getAssetLabel()));

            // add item group and account
            if ($row->getItem()->getItemGroup() !== null) {

                $inventory_account_id = null;
                if ($row->getItem()
                    ->getItemGroup()
                    ->getInventoryAccount() !== null) {
                    $inventory_account_id = $row->getItem()
                        ->getItemGroup()
                        ->getInventoryAccount()
                        ->getId();
                }

                $cogs_account_id = null;
                if ($row->getItem()
                    ->getItemGroup()
                    ->getCogsAccount() !== null) {
                    $cogs_account_id = $row->getItem()
                        ->getItemGroup()
                        ->getCogsAccount()
                        ->getId();
                }

                $item_cost_center_id = null;
                if ($row->getItem()
                    ->getItemGroup()
                    ->getCostCenter() !== null) {
                    $item_cost_center_id = $row->getItem()
                        ->getItemGroup()
                        ->getCostCenter()
                        ->getId();
                }

                $doc->addField(Field::UnIndexed('inventory_account_id', $inventory_account_id));
                $doc->addField(Field::UnIndexed('cogs_account_id', $cogs_account_id));
                $doc->addField(Field::UnIndexed('cost_center_id', $item_cost_center_id));
            } else {
                $doc->addField(Field::UnIndexed('inventory_account_id', null));
                $doc->addField(Field::UnIndexed('cogs_account_id', null));
                $doc->addField(Field::UnIndexed('cost_center_id', null));
            }

            $s = $row->getItem()->getAssetLabel();
            $l = strlen($s);
            $l > 3 ? $p = strpos($s, "-", 3) + 1 : $p = 0;

            $doc->addField(Field::Text('asset_label_lastnumber', substr($s, $p, $l - $p) * 1));
        }

        $index->addDocument($doc);
    }

    /**
     *
     * @return string
     */
    public function optimizeIndex()
    {

        // take long time
        set_time_limit(1500);
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            $index->optimize();
            return 'Index has been optimzed!<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     *
     * @param string $q
     * @param number $vendor_id
     * @return string[]|array[]|\ZendSearch\Lucene\Search\QueryHit[]|string[]|NULL[]
     */
    public function search($q, $vendor_id = null)
    {
        try {

            $index = Lucene::open(getcwd() . self::ITEM_INDEX);

            $final_query = new Boolean();

            $q = strtolower($q);

            $terms = explode(" ", $q);

            if (count($terms) > 1) {

                foreach ($terms as $t) {

                    $t = preg_replace('/\s+/', '', $t);

                    if (strlen($t) == 0) {
                        continue;
                    }

                    if (strpos($t, '*') != false) {
                        $pattern = new Term($t);
                        $query = new Wildcard($pattern);
                        $final_query->addSubquery($query, true);
                    } else {

                        $subquery = new MultiTerm();
                        $subquery->addTerm(new Term($t));
                        $final_query->addSubquery($subquery, true);
                    }
                }
            } else {

                if (strpos($q, '*') != false) {
                    $pattern = new Term($q);
                    $query = new Wildcard($pattern);
                    $final_query->addSubquery($query, true);
                } else {
                    $subquery = new MultiTerm();
                    $subquery->addTerm(new Term($q));
                    $final_query->addSubquery($subquery, true);
                }
            }

            if ($vendor_id !== null) {
                /*
                 * $subquery = new MultiTerm();
                 * $subquery->addTerm(new Term($vendor_id, 'vendor_id'));
                 */
                $subquery = new \ZendSearch\Lucene\Search\Query\Term(new Term('vendor_id_keyword=' . $vendor_id, 'vendor_id_keyword'));
                $final_query->addSubquery($subquery, true);
            }

            $subquery = new \ZendSearch\Lucene\Search\Query\Term(new Term(\Application\Model\Constants::DOC_STATUS_POSTED, 'po_doc_status'));
            $final_query->addSubquery($subquery, true);

            // var_dump ( $final_query );
            // echo $final_query->__toString();

            $hits = $index->find($final_query);

            $result = [
                "message" => count($hits) . " result(s) found for query: <b>" . $q . "</b>",
                "hits" => $hits
            ];

            return $result;
        } catch (\Exception $e) {
            $result = [
                "message" => 'Query: <b>' . $q . '</b> sent , but exception catched: <b>' . $e->getMessage() . "</b>\n",
                "hits" => null
            ];
            return $result;
        }
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \Inventory\Service\ItemSearchService
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }
}
