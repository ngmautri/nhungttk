<?php
namespace Inventory\Application\Service\Search\ZendSearch\PO;

use Application\Application\Service\Search\Contracts\QueryFilterInterface;
use Application\Application\Service\Search\Contracts\SearchResult;
use Application\Service\AbstractService;
use Inventory\Application\Service\Search\ZendSearch\Item\SearchIndexer;
use Inventory\Application\Service\Search\ZendSearch\Item\Filter\ItemQueryFilter;
use Procure\Application\Service\Search\ZendSearch\PO\PoSearch;
use Procure\Domain\Service\Search\PoSearchQueryInterface;
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
class ItemSearchQueryImpl extends AbstractService implements PoSearchQueryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Search\PoSearchQueryInterface::search()
     */
    public function search($q, QueryFilterInterface $filter = null)
    {
        try {

            $message = null;
            $hits = null;
            $query = $q;
            $queryString = null;

            $index = Lucene::open(getcwd() . PoSearch::INDEX_PATH);

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

            if ($filter instanceof ItemQueryFilter) {

                if ($filter->getIsFixedAsset() == 1) {

                    $format = \sprintf(SearchIndexer::FIXED_ASSET_VALUE);
                    $v = \sprintf($format, $filter->getIsFixedAsset());

                    $subquery = new \ZendSearch\Lucene\Search\Query\Term(new Term($v, SearchIndexer::FIXED_ASSET_KEY));
                    $final_query->addSubquery($subquery, true);
                }
            }

            $hits = $index->find($final_query);
            $queryString = $final_query->__toString();

            $message = \sprintf("%s result(s) found for query:<b>%s</b>", count($hits), $q);
        } catch (\Exception $e) {
            $message = sprintf("Failed: <b>%s</b>", $e->getMessage());
        }

        return new SearchResult($query, $queryString, $message, $hits);
    }
}
