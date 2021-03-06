<?php
namespace BP\Service;

use Doctrine\ORM\EntityManager;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use ZendSearch\Lucene\Analysis\Analyzer\Common\TextNum\CaseInsensitive;
// use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive; // Not worked
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Application\Entity\NmtInventoryItem;
use ZendSearch\Lucene\Search\Query\Boolean;
use ZendSearch\Lucene\Search\QueryParser;
use Exception;
use Application\Entity\NmtBpVendor;

/**
 *
 * @author nmt
 *        
 */
class VendorSearchService
{

    const ITEM_INDEX = "/data/bp/indexes/vendor";

    protected $doctrineEM;

    /**
     *
     * @return string
     */
    public function createVendorIndex()
    {

        // take long time
        set_time_limit(1500);

        $result = array();
        $sucess = 0;
        $fail = 0;

        try {
            $index = Lucene::create(getcwd() . self::ITEM_INDEX);

            Analyzer::setDefault(new CaseInsensitive());

            $row = new NmtBpVendor();

            $records = $this->doctrineEM->getRepository('Application\Entity\NmtBPVendor')->findAll();

            if (count($records) > 0) {

                foreach ($records as $r) {

                    try {
                        $doc = new Document();
                        $row = $r;
                        $doc->addField(Field::UnIndexed('vendor_id', $row->getId()));
                        $doc->addField(Field::UnIndexed('token', $row->getToken()));
                        $doc->addField(Field::UnIndexed('checksum', $row->getChecksum()));
                        $doc->addField(Field::Keyword('vendor_token_keyword', $row->getToken() . "__" . $row->getId()));
                        $doc->addField(Field::Keyword('vendor_number', $row->getVendorNumber()));

                        if ($row->getVendorNumber() !== null) {
                            $doc->addField(Field::Keyword('vendor_number_03', substr($row->getVendorNumber(), - 3)));
                        }

                        $doc->addField(Field::text('vendor_name', $row->getVendorName()));
                        $doc->addField(Field::text('vendor_name_short', $row->getVendorShortName()));
                        $doc->addField(Field::text('keywords', $row->getKeywords()));
                        $doc->addField(Field::text('remarks', $row->getRemarks()));
                        $doc->addField(Field::text('country_name', $row->getCountry()
                            ->getCountryName()));
                        $doc->addField(Field::Keyword('is_active', $row->getIsActive()));

                        $index->addDocument($doc);
                        // $log [] = $row->getVendorName () . " added!";
                        $sucess ++;
                    } catch (Exception $e) {
                        // $log [] = $e->getMessage ();
                        $fail ++;
                    }
                }
                $index->optimize();

                // $log [] = 'Vendor indexes is created successfully!<br> Index Size:' . $index->count () . '<br>Documents: ' . $index->numDocs ();

                $result = array(
                    'message' => 'Vendor indexes is created successfully!<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs(),
                    'sucess' => $sucess,
                    'fail' => $fail
                );
                return $result;
            } else {

                $result = array(
                    'message' => 'Nothing for indexing!',
                    'sucess' => $sucess,
                    'fail' => $fail
                );
                return $result;
            }
        } catch (Exception $e) {

            $result = array(
                'message' => $e->getMessage(),
                'sucess' => $sucess,
                'fail' => $fail
            );
            return $result;
        }
    }

    public function updateIndex($is_new = 0, $row, $optimized)
    {

        // take long time
        set_time_limit(1500);
        try {

            /** @var \Application\Entity\NmtBpVendor $row;*/
            if ($row instanceof NmtBpVendor) {

                $index = Lucene::open(getcwd() . self::ITEM_INDEX);
                Analyzer::setDefault(new CaseInsensitive());

                if ($is_new !== 1) {
                    $ck_query = 'vendor_token_keyword:' . $row->getToken() . '__' . $row->getId();
                    $ck_hits = $index->find($ck_query);

                    if (count($ck_hits) == 1) {
                        $ck_hit = $ck_hits[0];
                        $index->delete($ck_hit->id);
                    }
                }

                $doc = new Document();
                $doc->addField(Field::UnIndexed('vendor_id', $row->getId()));
                $doc->addField(Field::UnIndexed('token', $row->getToken()));
                $doc->addField(Field::UnIndexed('checksum', $row->getChecksum()));
                $doc->addField(Field::Keyword('vendor_token_keyword', $row->getToken() . "__" . $row->getId()));
                $doc->addField(Field::Keyword('vendor_number', $row->getVendorNumber()));

                if ($row->getVendorNumber() !== null) {
                    $doc->addField(Field::Keyword('vendor_number_03', substr($row->getVendorNumber(), - 3)));
                }

                $doc->addField(Field::text('vendor_name', $row->getVendorName()));
                $doc->addField(Field::text('vendor_name_short', $row->getVendorShortName()));
                $doc->addField(Field::text('keywords', $row->getKeywords()));
                $doc->addField(Field::text('remarks', $row->getRemarks()));
                $doc->addField(Field::text('country_name', $row->getCountry()
                    ->getCountryName()));
                $doc->addField(Field::Keyword('is_active', $row->getIsActive()));

                $index->addDocument($doc);

                if (optimized === true) {
                    $index->optimize();
                }
                return $row->getVendorName() . " has been updated /added successfully!";
            } else {
                return 'Input is invalid';
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function addDocument($item, $optimized)
    {

        // take long time
        set_time_limit(1500);
        try {
            $index = Lucene::open(getcwd() . self::ITEM_INDEX);
            Analyzer::setDefault(new CaseInsensitive());

            $row = new NmtBpVendor();

            if ($item instanceof NmtBpVendor) {
                $doc = new Document();
                $row = $item;

                $doc->addField(Field::UnIndexed('vendor_id', $row->getId()));
                $doc->addField(Field::UnIndexed('token', $row->getToken()));
                $doc->addField(Field::UnIndexed('checksum', $row->getChecksum()));

                $doc->addField(Field::text('vendor_name', $row->getVendorName()));
                $doc->addField(Field::text('vendor_name_short', $row->getVendorShortName()));
                $doc->addField(Field::text('keywords', $row->getKeywords()));
                $doc->addField(Field::text('remarks', $row->getRemarks()));
                $doc->addField(Field::text('country_name', $row->getCountry()
                    ->getCountryName()));
                $doc->addField(Field::Keyword('is_active', $row->getIsActive()));

                $index->addDocument($doc);

                if (optimized === true) {
                    $index->optimize();
                }

                return 'Document has been added successfully !<br> Index Size:' . $index->count() . '<br>Documents: ' . $index->numDocs();
            } else {
                return 'Input is invalid';
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

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

    public function search($q, $isActive = 1)
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

            $subquery1 = new MultiTerm();
            $subquery1->addTerm(new Term($isActive, 'is_active'));
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
