<?php
namespace Inventory\Controller;

use Inventory\Service\ItemSearchService;
use Inventory\Services\ArticleSearchService;
use Inventory\Services\AssetSearchService;
use Inventory\Services\SparePartsSearchService;
use User\Model\UserTable;
use ZendSearch\Lucene\Index\Term;
use ZendSearch\Lucene\Search\Query\Wildcard;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SearchController extends AbstractActionController
{

    protected $itemSearchService;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function optimizeItemIndexAction()
    {
        $results = $this->itemSearchService->optimizeIndex();

        return new ViewModel(array(
            'message' => $results
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function itemAction()
    {

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $q = $this->params()->fromQuery('q');
        $layout = $this->params()->fromQuery('layout');
        
        if($layout==null){
            $layout ='grid';
        }

        if ($q !== "") {
            $results = $this->itemSearchService->searchAll($q);
        } else {
            $results = [
                "message" => "",
                "hits" => null,
                'nmtPlugin' => $nmtPlugin
            ];
        }

        // var_dump($results);
        $viewModel =  new ViewModel(array(
            'message' => $results["message"],
            'hits' => $results["hits"],
            'nmtPlugin' => $nmtPlugin,
            'q' => $q,
        ));
        
        if ($layout == "grid") {
            $viewModel->setTemplate("inventory/search/item-grid");
        }
        
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function assetItemAction()
    {
        $q = $this->params()->fromQuery('q');

        if ($q !== "") {
            $results = $this->itemSearchService->searchAssetItem($q);
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
    public function sparepartItemAction()
    {
        $q = $this->params()->fromQuery('q');

        if ($q !== "") {
            $results = $this->itemSearchService->searchSPItem($q);
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
    public function createIndexAction()
    {
        $message = $this->itemSearchService->createItemIndex();

        return new ViewModel(array(
            'message' => $message
        ));
    }

    /**
     *
     * @deprecated
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Stdlib\ResponseInterface
     */
    public function assetAction()
    {

        // $query = $this->params ()->fromQuery ( 'query' );
        $q = $this->params()->fromQuery('query');
        $json = (int) $this->params()->fromQuery('json');

        if ($q == '') {
            return new ViewModel(array(
                'hits' => null
            ));
        }

        if (strpos($q, '*') !== false) {
            $pattern = new Term($q);
            $query = new Wildcard($pattern);
            $hits = $this->assetSearchService->search($query);
        } else {
            $hits = $this->assetSearchService->search($q);
        }

        if ($json === 1) {

            $data = array();

            foreach ($hits as $key => $value) {
                $n = (int) $key;
                $data[$n]['id'] = $value->asset_id;
                $data[$n]['name'] = $value->name;
                $data[$n]['tag'] = $value->tag;
            }

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            return $response;
        }

        return new ViewModel(array(
            'query' => $q,
            'hits' => $hits
        ));
    }

    /**
     *
     * @deprecated
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function asset1Action()
    {
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/text');
        $response->setContent('Test');
        return $response;
    }

    /**
     *
     * @deprecated
     *
     */
    public function sparepartAction()
    {

        // $query = $this->params ()->fromQuery ( 'query' );
        $q = $this->params()->fromQuery('query');
        $json = (int) $this->params()->fromQuery('json');

        if ($q == '') {
            return new ViewModel(array(
                'hits' => null
            ));
        }

        if (strpos($q, '*') !== false) {
            $pattern = new Term($q);
            $query = new Wildcard($pattern);
            $hits = $this->sparePartSearchService->search($query);
        } else {
            $hits = $this->sparePartSearchService->search($q);
        }

        if ($json === 1) {

            $data = array();

            foreach ($hits as $key => $value) {
                $n = (int) $key;
                $data[$n]['id'] = $value->sparepart_id;
                $data[$n]['name'] = $value->name;
                $data[$n]['tag'] = $value->tag;
                $data[$n]['code'] = $value->code;
            }

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            return $response;
        }

        return new ViewModel(array(
            'query' => $q,
            'hits' => $hits
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel|\Zend\Stdlib\ResponseInterface
     */
    public function articleAction()
    {

        // $query = $this->params ()->fromQuery ( 'query' );
        $user_id = $this->UserPlugin()->getUser()['id'];
        $user = $this->userTable->getUserDepartment($user_id);

        if (! $user == null) {
            $department = $user->department_id;
        } else {
            $department = 0;
        }

        // var_dump($department);

        $q = $this->params()->fromQuery('query');
        $json = (int) $this->params()->fromQuery('json');

        if ($q == '') {
            return new ViewModel(array(
                'hits' => null
            ));
        }

        if (strpos($q, '*') != false) {
            $pattern = new Term($q);
            $query = new Wildcard($pattern);
            $hits = $this->articleSearchService->search($query, $department);
        } else {
            $hits = $this->articleSearchService->search($q, $department);
        }

        if ($json === 1) {

            $data = array();

            foreach ($hits as $key => $value) {
                $n = (int) $key;
                $data[$n]['id'] = $value->article_id;
                $data[$n]['name'] = $value->name;
                $data[$n]['description'] = $value->description;
                $data[$n]['code'] = $value->code;
            }

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode($data));
            return $response;
        }

        return new ViewModel(array(
            'query' => $q,
            'hits' => $hits
        ));
    }

    /**
     *
     * @deprecated
     * @return \Zend\View\Model\ViewModel|\Zend\Stdlib\ResponseInterface
     */
    public function allArticleAction()
    {
        $q = $this->params()->fromQuery('query');
        $json = (int) $this->params()->fromQuery('json');

        if ($q == '') {
            return new ViewModel(array(
                'hits' => null
            ));
        }

        if (strpos($q, '*') != false) {
            $pattern = new Term($q);
            $query = new Wildcard($pattern);
            $hits = $this->articleSearchService->search($query, 0);
        } else {
            $hits = $this->articleSearchService->search($q, 0);
        }

        if ($json === 1) {

            $data = array();

            foreach ($hits as $key => $value) {
                $n = (int) $key;
                $data[$n]['id'] = $value->article_id;
                $data[$n]['name'] = $value->name;
                $data[$n]['description'] = $value->description;
                $data[$n]['code'] = $value->code;
            }

            $response = $this->getResponse();
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
			$response->setContent(json_encode($data));
			return $response;
		}
	
		return new ViewModel ( array (
				'query' => $q,
				'hits' => $hits,
		));
	}
	

	public function setItemSearchService(ItemSearchService $itemSearchService) {
		$this->itemSearchService = $itemSearchService;
		return $this;
	}

}



