<?php
namespace Procure\Service;

use Application\Service\AbstractService;
use Doctrine\ORM\EntityManager;
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
class QoSearchService extends AbstractService
{

    const ITEM_INDEX = "/data/procure/indexes/qo";

    protected $doctrineEM;

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

            $query = 'SELECT e, i, inv FROM \Application\Entity\FinVendorInvoiceRow e JOIN e.item i JOIN e.invoice inv Where 1=?1';
            $records = $this->doctrineEM->createQuery($query)
                ->setParameters(array(
                "1" => 1
            ))
                ->getResult();

            if (count($records) > 0) {

                foreach ($records as $row) {
                    $this->_addDocument($index, $row);
                }
                $index->optimize();
                $log1 = 'PR indexes is created successfully!<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();
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
    public function indexingAPRows($ap_rows, $optimized = TRUE)
    {
        if (count($ap_rows) == 0) {
            return 'No AP rows found';
        }

        // take long time
        set_time_limit(1500);

        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());

            foreach ($ap_rows as $row) {

                /** @var \Application\Entity\FinVendorInvoiceRow $row ; */
                if (! $row instanceof \Application\Entity\FinVendorInvoiceRow) {
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
     * @param \Application\Entity\FinVendorInvoiceRow $row
     */
    private function _addDocument(\ZendSearch\Lucene\SearchIndexInterface $index, \Application\Entity\FinVendorInvoiceRow $row)
    {
        if ($index == null || ! $row instanceof \Application\Entity\FinVendorInvoiceRow) {
            return;
        }

        if ($row->getInvoice() == null) {
            return;
        }

        $doc = new Document();

        $doc->addField(Field::UnIndexed('ap_row_id', $row->getId()));
        $doc->addField(Field::UnIndexed('ap_row_token', $row->getToken()));

        $doc->addField(Field::Keyword('row_token_keyword', $row->getToken() . "__" . $row->getId()));
        $doc->addField(Field::Keyword('row_identifer_keyword', $row->getRowIdentifer()));

        $doc->addField(Field::UnIndexed('row_quantity', $row->getQuantity()));
        $doc->addField(Field::UnIndexed('row_conversion_factor', $row->getConversionFactor()));
        $doc->addField(Field::UnIndexed('row_unit', $row->getUnit()));
        $doc->addField(Field::text('row_remarks', $row->getRemarks()));
        $doc->addField(Field::text('row_name', $row->getVendorItemCode()));

        $doc->addField(Field::UnIndexed('ap_id', $row->getInvoice()
            ->getId()));
        $doc->addField(Field::UnIndexed('ap_token', $row->getInvoice()
            ->getToken()));

        $doc->addField(Field::text('ap_number', $row->getInvoice()
            ->getInvoiceNo()));

        $doc->addField(Field::Keyword('ap_sys_number', $row->getInvoice()
            ->getSysNumber()));

        $doc->addField(Field::Keyword('ap_doc_status', $row->getInvoice()
            ->getDocStatus()));

        if ($row->getInvoice()->getVendor() != null) {
            $doc->addField(Field::Keyword('vendor_id_keyword', 'vendor_id_keyword=' . $row->getInvoice()
                ->getVendor()
                ->getId()));
            $doc->addField(Field::UnIndexed('vendor_id', $row->getInvoice()
                ->getVendor()
                ->getId()));

            $doc->addField(Field::text('vendor_name', $row->getInvoice()
                ->getVendor()
                ->getVendorName()));
        } else {
            $doc->addField(Field::Keyword('vendor_id_keyword', "vendor_id_keyword="));
            $doc->addField(Field::UnIndexed('vendor_name', ""));
            $doc->addField(Field::UnIndexed('vendor_id', ""));
        }

        if ($row->getPoRow() !== null) {

            if ($row->getPoRow()->getPo() !== null) {

                /** @var \Application\Entity\NmtProcurePo $po ; */

                $po = $row->getPoRow()->getPo();
                $doc->addField(Field::UnIndexed('po_id', $po->getId()));
                $doc->addField(Field::UnIndexed('po_token', $po->getToken()));
                $doc->addField(Field::text('po_number', $po->getContractNo()));
                $doc->addField(Field::Keyword('po_sys_number', $po->getSysNumber()));
            }
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

            $subquery = new \ZendSearch\Lucene\Search\Query\Term(new Term(\Application\Model\Constants::DOC_STATUS_POSTED, 'ap_doc_status'));
            $final_query->addSubquery($subquery, true);

            // var_dump ( $final_query );
            //echo $final_query->__toString();

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
