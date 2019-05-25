<?php
namespace Inventory\Application\Service\Search\Solr;

use Application\Service\AbstractService;
use Inventory\Domain\Service\Search\ItemSearchInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;

/**
 * SOlr integration
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSearchService extends AbstractService implements ItemSearchInterface
{
    const SOLR_URI = "http://localhost:8983/solr/inventory_item/select?rows=1000";
    
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Service\Search\ItemSearchInterface::search()
     */
    public function search($q)
    {
        $client = new HttpClient();
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');

        $client->setUri(self::SOLR_URI);
        $client->setMethod('GET');
        $client->setParameterGET(array(
            'q' => "name:" . $q
        ));
        $response = $client->send();

        if (! $response->isSuccess()) {
            // report failure
            $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
            $message = $message . $client->getMethod();
            $message = $message . "...........NO unknown....";
            return $message;
        }

        $body = $response->getBody();
        return $body;
    }

    public function optimizeIndex()
    {}

    public function createDoc($doc, $isNew = true)
    {}

    public function searchFixedAsset($q)
    {}

    public function searchInventoryItem($q)
    {}

    public function searchServiceItem($q)
    {}

    public function createIndex()
    {}
    public function updateItemIndex($itemId, $isNew = true, $optimized = false)
    {}

}
