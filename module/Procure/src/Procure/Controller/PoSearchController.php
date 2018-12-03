<?php
namespace Procure\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Procure\Service\PrSearchService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoSearchController extends AbstractActionController
{

    protected $doctrineEM;

    protected $poSearchService;

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function autocompleteAction()
    {
        /* retrieve the search term that autocomplete sends */
        $q = trim(strip_tags($_GET['term']));
        //$vendor_id = (int) $this->params ()->fromQuery ( 'vendor_id' );
        
        if(isset($_REQUEST['vendor_id'])){
            $vendor_id= (int) $_REQUEST['vendor_id'];
        }else{
            $vendor_id=0;
        }
        

        $a_json = array();
        $a_json_row = array();

        if ($q !== "") {
            $results = $this->poSearchService->search($q, $vendor_id);

            if (count($results) > 0) {
                foreach ($results['hits'] as $a) {
                    $a_json_row["value"] = $a->row_identifer_keyword . ' | ' . $a->item_name;

                    $a_json_row["po_id"] = $a->po_id;
                    $a_json_row["po_token"] = $a->po_token;
                    $a_json_row["po_number"] = $a->po_number;
                    $a_json_row["po_sys_number"] = $a->po_sys_number;
                    
                    $a_json_row["vendor_id"] = $a->vendor_id;
                    $a_json_row["vendor_name"] = $a->vendor_name;
                    
                    $a_json_row["po_row_id"] = $a->po_row_id;
                    $a_json_row["token"] = $a->token;
                    $a_json_row["row_identifer_keyword"] = $a->row_identifer_keyword;
                    
                    $a_json_row["item_id"] = $a->item_id;
                    $a_json_row["item_token"] = $a->item_token;
                    $a_json_row["item_checksum"] = $a->item_checksum;

                    $a_json_row["item_name"] = $a->item_name;
                    $a_json_row["item_sku_key"] = $a->item_sku_key;
                    $a_json_row["manufacturer_code"] = $a->manufacturer_code;
                    $a_json_row["manufacturer_code_key"] = $a->manufacturer_code_key;
                    
                    $a_json_row["inventory_account_id"] = $a->inventory_account_id;
                    $a_json_row["cogs_account_id"] = $a->cogs_account_id;
                    $a_json_row["cost_center_id"] = $a->cost_center_id;
                    
                    
                    $a_json_row["row_quantity"] = $a->row_quantity;
                    $a_json_row["row_unit"] = $a->row_unit;
                    $a_json_row["row_name"] = $a->row_name;
                    $a_json_row["row_unit_price"] = $a->row_unit_price;
                    
                    $a_json_row["row_conversion_factor"] = $a->row_conversion_factor;

                    $a_json[] = $a_json_row;
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
            $results = $this->poSearchService->search($q);
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
        $request = $this->getRequest();

        // accepted only ajax request

        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

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
                $results = $this->prSearchService->search($q);
            }
        }

        // var_dump($results);
        return new ViewModel(array(
            'message' => $results["message"],
            'hits' => $results["hits"],
            'context' => $context,
            'target_id' => $target_id,
            'target_name' => $target_name
        ));
    }

    /**
     */
    public function createIndexAction()
    {
        $result = $this->poSearchService->createIndex();
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

    /**
     *
     * @return \Procure\Service\PoService
     */
    public function getPoSearchService()
    {
        return $this->poSearchService;
    }

    /**
     *
     * @param \Procure\Service\PoService $poSearchService
     */
    public function setPoSearchService(\Procure\Service\PoSearchService $poSearchService)
    {
        $this->poSearchService = $poSearchService;
    }
	
}
