<?php
namespace Procure\Application\Service\Search\ZendSearch\PO;

use Application\Application\Service\Search\Contracts\SearchResult;
use Application\Service\AbstractService;
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
class PoSearchQueryImpl extends AbstractService implements PoSearchQueryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Service\Search\PoSearchQueryInterface::search()
     */
    public function search($q, $vendor_id = null)
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

            if ($vendor_id !== null) {
                $subquery = new \ZendSearch\Lucene\Search\Query\Term(new Term('vendor_id_key_' . $vendor_id, 'vendor_id_key'));
                $final_query->addSubquery($subquery, true);
            }

            $subquery = new \ZendSearch\Lucene\Search\Query\Term(new Term(\Application\Model\Constants::DOC_STATUS_POSTED, 'po_doc_status'));
            $final_query->addSubquery($subquery, true);

            $hits = $index->find($final_query);

            $queryString = $final_query->__toString();
            $message = \sprintf("%s result(s) found for query:<b>%s</b>", count($hits), $q);
        } catch (\Exception $e) {
            $message = sprintf("Failed: <b>%s</b>", $e->getMessage());
        }

        return new SearchResult($query, $queryString, $message, $hits);
    }
}
