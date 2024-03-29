<?php
namespace HR\Application\Service\Search\ZendSearch\Individual;

use Application\Application\Service\Search\Contracts\QueryFilterInterface;
use Application\Application\Service\Search\Contracts\SearchResult;
use Application\Service\AbstractService;
use HR\Application\Service\Search\ZendSearch\Individual\Filter\IndividualQueryFilter;
use HR\Domain\Service\Search\IndividualSearchQueryInterface;
use Inventory\Domain\Item\ItemSnapshotAssembler;
use ZendSearch\Lucene\Lucene;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\Boolean;
use ZendSearch\Lucene\Search\Query\MultiTerm;
use ZendSearch\Lucene\Search\Query\Wildcard;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class IndividualSearchQueryImpl extends AbstractService implements IndividualSearchQueryInterface
{

    public function search($q, QueryFilterInterface $filter = null)
    {
        try {

            $message = null;
            $hits = null;
            $query = $q;
            $queryString = null;

            $index = Lucene::open(getcwd() . SearchIndexer::INDEX_PATH);

            $final_query = $this->_createQuery($q, $filter);
            $hits = $index->find($final_query);
            $queryString = $final_query->__toString();

            $message = \sprintf("%s result(s) found for query:<b>%s</b>", count($hits), $q);
            $this->logInfo($message);
        } catch (\Exception $e) {
            $message = sprintf("Failed: <b>%s</b>", $e->getMessage());
            $this->logAlert($message);
            $this->logException($e);
        }

        return new SearchResult($query, $queryString, $message, $hits);
    }

    public function searchMainItem($q, QueryFilterInterface $filter = null)
    {
        try {

            $message = null;
            $hits = null;
            $query = $q;
            $queryString = null;

            $index = Lucene::open(getcwd() . SearchIndexer::INDEX_PATH);

            $final_query = $this->_createQuery($q, $filter);

            /*
             * |=================================
             * | Exclude: Variant
             * |
             * |==================================
             */
            $subquery = new \ZendSearch\Lucene\Search\Query\Term(new Term(SearchIndexer::NO, SearchIndexer::VARIANT_KEY));
            $final_query->addSubquery($subquery, true);

            /*
             * |=================================
             * | Exclude: Serial
             * |
             * |==================================
             */
            $subquery = new \ZendSearch\Lucene\Search\Query\Term(new Term(SearchIndexer::NO, SearchIndexer::SERIAL_KEY));
            $final_query->addSubquery($subquery, true);

            $hits = $index->find($final_query);
            $queryString = $final_query->__toString();

            $message = \sprintf("%s result(s) found for query:<b>%s</b>", count($hits), $q);
            $this->logInfo($message);
        } catch (\Exception $e) {
            $message = sprintf("Failed: <b>%s</b>", $e->getMessage());
            $this->logAlert($message);
            $this->logException($e);
        }

        return new SearchResult($query, $queryString, $message, $hits);
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Service\Search\IndividualSearchQueryInterface::queryForAutoCompletion()
     */
    public function queryForAutoCompletion($q, QueryFilterInterface $filter, $maxHit = 10, $returnDetails = true)
    {
        $results = $this->searchMainItem($q, $filter);
        $results_array = [];

        $hits_array = [];
        $n = 0;

        foreach ($results->getHits() as $hit) {
            $n ++;

            if ($n > $maxHit) {
                break;
            }

            $item_thumbnail = '/images/no-pic1.jpg';
            if ($hit->item_thumbnail != null) {
                $item_thumbnail = $hit->item_thumbnail;
            }

            $hits_array["item_thumbnail"] = $item_thumbnail;

            $hits_array["n"] = \sprintf('%s/%s found.', $n, $results->getTotalHits());
            if ($n > $maxHit - 3) {
                $hits_array["n"] = \sprintf('%s/%s found. There are %s hits more...', $n, $results->getTotalHits(), $results->getTotalHits() - $n);
            }
            $hits_array["value"] = \sprintf('%s | %s', $hit->itemSku, $hit->itemName);

            if ($returnDetails) {
                $hits_array["hit"] = ItemSnapshotAssembler::createFromQueryHit($hit);
            } else {
                $hits_array["hit"] = null;
            }
            $results_array[] = $hits_array;
        }

        return ($results_array);
    }

    /**
     *
     * @param string $q
     * @param QueryFilterInterface $filter
     * @return \ZendSearch\Lucene\Search\Query\Boolean
     */
    private function _createQuery($q, QueryFilterInterface $filter = null)
    {
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

        if ($filter instanceof IndividualQueryFilter) {

            /*
             * if ($filter->getIsEmployee() == 1) {
             *
             * $format = \sprintf(SearchIndexer::FIXED_ASSET_VALUE);
             * $v = \sprintf($format, $filter->getIsFixedAsset());
             *
             * $subquery = new \ZendSearch\Lucene\Search\Query\Term(new Term($v, SearchIndexer::FIXED_ASSET_KEY));
             * $final_query->addSubquery($subquery, true);
             * }
             */
        }

        return $final_query;
    }
}
