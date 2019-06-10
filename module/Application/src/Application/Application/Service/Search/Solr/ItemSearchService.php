<?php
namespace Inventory\Application\Service\Search\Solr;

use Application\Notification;
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
     * {@inheritdoc}
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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Search\ItemSearchInterface::updateItemIndex()
     */
    public function updateItemIndex($itemId, $isNew = true, $optimized = false)
    {
        $notification = new Notification();

        if ($itemId == null)
            return ($notification->addError("ItemId is empty. Nothing to index"));

        $rep = new \Inventory\Infrastructure\Persistence\DoctrineItemReportingRepository();
        $rep->setDoctrineEM($this->getDoctrineEM());
        $records = $rep->getAllItemWithSerial($itemId);

        if (count($records) == 0) {
            $notification->setErrors(sprintf("[INFO] Nothing for indexing"));
            return $notification;
        }

        $data = array();

        foreach ($records as $row) {
            $data[] = $this->_createDocument($row);
        }

        var_dump(json_encode($data));

        $client = new HttpClient();
        $request = new Request();
        $request->setUri('http://localhost:8983/solr/inventory_item/update/json/docs?commit=true');
        $request->setMethod('POST');
        $request->setContent(json_encode($data));

        $request->getHeaders()->addHeaders(array(
            'Content-Type' => 'application/json'
        ));

        // $client->setHeaders('Content-type','application/json');
        $client->setEncType(HttpClient::ENC_FORMDATA);

        // if get/get-list/create
        $response = $client->send($request);

        if (! $response->isSuccess()) {
            // report failure
            $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
            $message .= $response->getContent();
            $message = $message . $client->getMethod();
            $message = $message . "...........NO unknown....";

            $response = $this->getResponse();
            $response->setContent($message);
            return $response;
        }

        $body = $response->getBody();
        var_dump($body);
    }

    /**
     *
     * @param \ZendSearch\Lucene\SearchIndexInterface $index
     * @param array $row
     */
    private function _createDocument($row)
    {
        if ($row == null)
            return;

        $data = array(
            "item_token_keyword" => "__" . $row['id'],
            'item_id' => $row['id'],
            'token' => $row['token'],
            'item_name' => $row['item_name'],
            'item_token_serial_keyword'=> $row['token'] . "__" . $row['id'] . "__" . $row['serial_id'],
            
        );

        return $data;
    }
}
