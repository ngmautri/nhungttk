<?php
namespace Procure\Service;

use Application\Entity\NmtProcurePrRow;
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
class PrSearchService extends AbstractService
{

    const ITEM_INDEX = "/data/procure/indexes/pr";

    protected $doctrineEM;

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $index
     * @param \Application\Entity\NmtProcurePrRow $row
     */
    private function _addDocument(\ZendSearch\Lucene\SearchIndexInterface $index, \Application\Entity\NmtProcurePrRow $row)
    {
        if ($index == null || ! $row instanceof \Application\Entity\NmtProcurePrRow) {
            return;
        }

        if ($row->getPr() == null) {
            return;
        }

        $doc = new Document();

        /** @var \Application\Entity\NmtProcurePrRow $row ; */

        $doc->addField(Field::UnIndexed('pr_row_id', $row->getId()));
        $doc->addField(Field::UnIndexed('token', $row->getToken()));
        $doc->addField(Field::Keyword('row_token_keyword', $row->getToken() . "__" . $row->getId()));
        $doc->addField(Field::Keyword('row_identifer_keyword', $row->getRowIdentifer()));
        $doc->addField(Field::Keyword('row_is_active_keyword', $row->getIsActive()));

        $doc->addField(Field::UnIndexed('checksum', $row->getChecksum()));
        $doc->addField(Field::UnIndexed('row_quantity', $row->getQuantity()));
        $doc->addField(Field::UnIndexed('row_conversion_factor', $row->getConversionFactor()));

        // $doc->addField ( Field::UnIndexed ( 'row_edt', $row->getEdt() ) );
        $doc->addField(Field::UnIndexed('row_unit', $row->getRowUnit()));
        $doc->addField(Field::text('row_remark', $row->getRemarks()));
        $doc->addField(Field::text('row_name', $row->getRowName()));

        $doc->addField(Field::UnIndexed('pr_id', $row->getPr()
            ->getId()));
        $doc->addField(Field::UnIndexed('pr_token', $row->getPr()
            ->getToken()));
        $doc->addField(Field::UnIndexed('pr_checksum', $row->getPr()
            ->getChecksum()));
        $doc->addField(Field::text('pr_number', $row->getPr()
            ->getPrNumber()));
        $doc->addField(Field::text('pr', $row->getPr()
            ->getPrNumber()));

        $doc->addField(Field::Keyword('pr_auto_number', $row->getPr()
            ->getPrAutoNumber()));

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
            $doc->addField(Field::Keyword('item_sku_key1', $row->getItem()
                ->getItemSku1()));
            $doc->addField(Field::Keyword('item_sku_key2', $row->getItem()
                ->getItemSku2()));
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
                
                $inventory_account_id =null;
                if( $row->getItem()->getItemGroup()->getInventoryAccount()!==null){
                    $inventory_account_id = $row->getItem()->getItemGroup()->getInventoryAccount()->getId();
                }
                
                $cogs_account_id =null;
                if( $row->getItem()->getItemGroup()->getCogsAccount()!==null){
                    $cogs_account_id = $row->getItem()->getItemGroup()->getCogsAccount()->getId();
                }
                
                $item_cost_center_id =null;
                if( $row->getItem()->getItemGroup()->getCostCenter()!==null){
                    $item_cost_center_id = $row->getItem()->getItemGroup()->getCostCenter()->getId();
                }
                 
                $doc->addField(Field::UnIndexed('inventory_account_id', $inventory_account_id));
                $doc->addField(Field::UnIndexed('cogs_account_id', $cogs_account_id));
                $doc->addField(Field::UnIndexed('cost_center_id', $item_cost_center_id));
            }else{
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
    public function createIndex()
    {

        // take long time
        set_time_limit(1500);
        try {
            $index = Lucene::create(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());

            $query = 'SELECT e, i, pr FROM Application\Entity\NmtProcurePrRow e JOIN e.item i JOIN e.pr pr Where 1=?1';
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
                $log1 = 'PR indexes is created successfully!<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();

                return $log1;
            } else {
                $log1 = 'Nothing for indexing!';

                return $log1;
            }
        } catch (Exception $e) {
            $log1 = 'Nothing for indexing!';

            return $log1;
        }
    }

    /**
     *
     * @param number $is_new
     * @param \Application\Entity\NmtProcurePrRow $row
     * @param boolean $optimized
     */
    public function updateIndex($is_new = 0, \Application\Entity\NmtProcurePrRow $row, $optimized)
    {
        if (! $row instanceof NmtProcurePrRow) {
            return;
        }

        // take long time
        set_time_limit(1500);
        try {

            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());

            if ($is_new !== 1) {
                $ck_query = 'row_token_keyword:' . $row->getToken() . '__' . $row->getId();
                $ck_hits = $index->find($ck_query);

                if (count($ck_hits) == 1) {
                    $ck_hit = $ck_hits[0];
                    $index->delete($ck_hit->id);
                }
            }

            /*
             * $query = 'SELECT e, i, pr FROM Application\Entity\NmtProcurePrRow e JOIN e.item i JOIN e.pr pr Where 1=?1 AND e.id = ?2';
             *
             * $records = $this->doctrineEM->createQuery($query)
             * ->setParameters(array(
             * "1" => 1,
             * "2" => $row->getId()
             * ))
             * ->getResult();
             */

            // alway found.
            // $row = $records[0];

            $this->_addDocument($index, $row);

            if ($optimized === true) {
                $index->optimize();
            }
            return $row->getId() . " index ID " . $ck_hit->id . " has been updated /added";
        } catch (Exception $e) {
            return $e->getMessage();
        }
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
     * @return string[]|array[]|\ZendSearch\Lucene\Search\QueryHit[]|string[]|NULL[]
     */
    public function search($q)
    {
        try {

            $index = Lucene::open(getcwd() . self::ITEM_INDEX);

            $final_query = new Boolean();

            $q = strtolower($q);

            $terms = explode(" ", $q);

            if (count($terms) > 1) {

                foreach ($terms as $t) {
                    
                    $t = preg_replace('/\s+/', '', $t);
                    
                    if(strlen($t) == 0 ){
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

            $hits = $index->find($final_query);

            /*
             * echo count ( $hits ) . " result(s) found for query: <b>" . $q . "</b>";
             *
             * $n=0;
             * foreach($hits as $h){
             * $n++;
             * echo $n. ": " . $h->getDocument()->getFieldUtf8Value("item_id") . "<br>";
             * $fields = $h->getDocument()->getFieldNames();
             *
             * foreach ($fields as $f){
             * echo $n. "--" . $f . "<br>";
             * }
             *
             *
             * }
             */

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
     * @param string $q
     * @return string[]|array[]|\ZendSearch\Lucene\Search\QueryHit[]|string[]|NULL[]
     */
    public function searchAssetItem($q)
    {
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);

            $final_query = new Boolean();

            if (strpos($q, '*') != false) {
                $pattern = new Term($q);
                $query = new Wildcard($pattern);
                $final_query->addSubquery($query, true);
            } else {
                $subquery = new MultiTerm();
                $subquery->addTerm(new Term($q));
                $final_query->addSubquery($subquery, true);
            }

            $subquery1 = new MultiTerm();
            $subquery1->addTerm(new Term('1', 'is_fixed_asset'));

            $final_query->addSubquery($subquery1, true);

            // var_dump ( $final_query );
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
     * @param string $q
     * @return string[]|array[]|\ZendSearch\Lucene\Search\QueryHit[]|string[]|NULL[]
     */
    public function searchSPItem($q)
    {
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);

            $final_query = new Boolean();

            if (strpos($q, '*') != false) {
                $pattern = new Term($q);
                $query = new Wildcard($pattern);
                $final_query->addSubquery($query, true);
            } else {
                $subquery = new MultiTerm();
                $subquery->addTerm(new Term($q));
                $final_query->addSubquery($subquery, true);
            }

            $subquery1 = new MultiTerm();
            $subquery1->addTerm(new Term('1', 'is_sparepart'));

            $final_query->addSubquery($subquery1, true);

            // var_dump ( $final_query );
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

    // Analyzer::setDefault ( new CaseInsensitive () );
    // $analyzer = Analyzer::getDefault ( new CaseInsensitive () );

    /*
     * foreach ($hits as $h){
     *
     * echo $query->htmlFragmentHighlightMatches($h->item_sku);
     * $token = $analyzer->tokenize($h->item_sku);
     * echo "TOKEN" . count($token);
     * foreach ($token as $t){
     * var_dump("T::" . $t->getTermText());
     * }
     * };
     */

    /*
     * foreach ($hits as $h){
     * var_dump($h->getDocument());
     * break;
     * }
     */

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
