<?php
namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Inventory\Service\ItemSearchService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSearchController extends AbstractActionController
{

    protected $doctrineEM;

    protected $itemSearchService;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        return new ViewModel(array());
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function autocompleteAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /* retrieve the search term that autocomplete sends */
        $q = trim(strip_tags($_GET['term']));
        // $q = $this->params ()->fromQuery ( 'q' );

        $a_json = array();
        $a_json_row = array();

        if ($q !== "") {
            $results = $this->itemSearchService->searchAll($q);
            if (count($results) > 0) {

                // $a_json['total_hit'] = count ( $results );
                $n = 0;
                foreach ($results['hits'] as $a) {
                    $n ++;
                    $a_json_row["n"] = $n;
                    $a_json_row["id"] = $a->item_id;
                    $a_json_row["token"] = $a->token;
                    $a_json_row["checksum"] = $a->checksum;
                    $a_json_row["value"] = $a->item_name;
                    $a_json_row["item_sku"] = $a->item_sku;
                    $a_json_row["item_serial"] = $a->manufacturer_code;
                    $a_json_row["item_thumbnail"] = $nmtPlugin->getItemPic($a->item_id);
                    $a_json_row["total_hit"] = count($results['hits']);

                    /*
                     * $a_json_row["inventory_account_id"] = $a->inventory_account_id;
                     * $a_json_row["cogs_account_id"] = $a->cogs_account_id;
                     * $a_json_row["cost_center_id"] = $a->cost_center_id;
                     */
                    // $a_json['hit']=$a_json_row;
                    $a_json[] = $a_json_row;

                    if ($n >= 15) {
                        break;
                    }
                }
            }
        }
        // var_dump($a_json);
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json));
        return $response;
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function autocomplete1Action()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /* retrieve the search term that autocomplete sends */
        $q = trim(strip_tags($_GET['term']));
        // $q = $this->params ()->fromQuery ( 'q' );

        $a_json = array();
        $a_json_row = array();

        if ($q !== "") {
            $results = $this->itemSearchService->searchAll($q);
            if (count($results) > 0) {

                $a_json['total_hit'] = count($results);
                $n = 1;
                foreach ($results['hits'] as $a) {
                    $a_json_row["item_id"] = $a->item_id;
                    $a_json_row["item_token"] = $a->token;
                    $a_json_row["item_checksum"] = $a->checksum;
                    $a_json_row["item_name"] = $a->item_name;
                    $a_json_row["item_sku"] = $a->item_sku;
                    $a_json_row["item_serial"] = $a->manufacturer_code;
                    $a_json_row["item_thumbnail"] = $nmtPlugin->getItemPic($a->item_id);

                    /*
                     * $a_json_row["inventory_account_id"] = $a->inventory_account_id;
                     * $a_json_row["cogs_account_id"] = $a->cogs_account_id;
                     * $a_json_row["cost_center_id"] = $a->cost_center_id;
                     */
                    $a_json[] = $a_json_row;
                    // $a_json[]=$a_json_row;
                    $n ++;

                    if ($n > 10) {
                        break;
                    }
                }
            }
        }
        // var_dump($a_json);
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($a_json));
        return $response;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function doAction()
    {
        $q = $this->params()->fromQuery('q');

        if ($q !== "") {
            $results = $this->itemSearchService->searchAll($q);
        } else {
            $results = [
                "message" => "",
                "hits" => null
            ];
        }

        // var_dump($results);
        return new ViewModel(array(
            'message' => $results["message"],
            'hits' => $results["hits"]
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function do1Action()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $request = $this->getRequest();

        // accepted only ajax request

        /*
         * if (! $request->isXmlHttpRequest ()) {
         * return $this->redirect ()->toRoute ( 'access_denied' );
         * }
         */

        $this->layout("layout/user/ajax");

        $q = $this->params()->fromQuery('q');
        $context = $this->params()->fromQuery('context');
        $target_id = $this->params()->fromQuery('target_id');
        $target_name = $this->params()->fromQuery('target_name');

        $results = [
            "message" => "",
            "hits" => null,
            'context' => $context,
            'target_id' => $target_id,
            'target_name' => $target_name
        ];

        if ($q !== null) {
            if ($q !== "") {
                $results = $this->itemSearchService->searchAll($q);
            }
        }

        // var_dump($results);
        return new ViewModel(array(
            'message' => $results["message"],
            'hits' => $results["hits"],
            'context' => $context,
            'target_id' => $target_id,
            'target_name' => $target_name,
            'nmtPlugin' => $nmtPlugin
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function createIndexAction()
    {
        $result = $this->itemSearchService->createIndex();
        return new ViewModel(array(
            'result' => $result
        ));
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
     * @return \BP\Controller\VendorController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    public function getItemSearchService()
    {
        return $this->itemSearchService;
    }

    public function setItemSearchService(ItemSearchService $itemSearchService)
    {
        $this->itemSearchService = $itemSearchService;
        return $this;
    }
}
