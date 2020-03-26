<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Service\ApplicationService;
use Application\Service\AppSearchService;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SearchController extends AbstractActionController
{

    protected $authService;

    protected $appService;

    protected $userTable;

    protected $appSearchService;

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface|NULL
     */
    public function testAction()
    {
        $message = $this->appSearchService->createResourcesIndexes();
        return new ViewModel(array(
            'message' => $message
        ));
    }

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface|NULL
     */
    public function resourceAction()
    {
        $q = $this->params()->fromQuery('q');

        if ($q !== "") {
            $results = $this->appSearchService->searchResource($q);
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

    // SETTER AND GETTER
    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthService($authService)
    {
        $this->authService = $authService;
        return $this;
    }

    public function getUserTable()
    {
        return $this->userTable;
    }

    public function setUserTable($userTable)
    {
        $this->userTable = $userTable;
        return $this;
    }

    public function getAppService()
    {
        return $this->appService;
    }

    public function setAppService(ApplicationService $appService)
    {
        $this->appService = $appService;
        return $this;
    }

    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    public function getAppSearchService()
    {
        return $this->appSearchService;
    }

    public function setAppSearchService(AppSearchService $appSearchService)
    {
        $this->appSearchService = $appSearchService;
        return $this;
    }
}
