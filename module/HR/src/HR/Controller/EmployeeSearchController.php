<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use MLA\Paginator;
use Application\Entity\NmtHrEmployee;
use Zend\Validator\Date;
use HR\Service\EmployeeSearchService;

/**
 *
 * @author nmt
 *        
 */
class EmployeeSearchController extends AbstractActionController
{

    protected $authService;

    protected $SmtpTransportService;

    protected $userTable;

    protected $doctrineEM;

    protected $employeeSearchService;

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {}

    /**
     *
     * {@inheritdoc}
     *
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function createIndexAction()
    {
        $message = $this->employeeSearchService->createEmployeeIndex();

        return new ViewModel(array(
            'message' => $message
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function doAction()
    {
        $q = $this->params()->fromQuery('q');

        if ($q !== "") {
            $results = $this->employeeSearchService->search($q);
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
    public function autocompleteAction()
    {
        /* retrieve the search term that autocomplete sends */
        $q = trim(strip_tags($_GET['term']));
        // $q = $this->params ()->fromQuery ( 'q' );

        $a_json = array();
        $a_json_row = array();

        if ($q !== "") {
            $results = $this->employeeSearchService->search($q);
            if (count($results) > 0) {
                foreach ($results['hits'] as $a) {
                    $a_json_row["id"] = $a->employee_id;
                    $a_json_row["value"] = $a->employee_name . ' - ' . $a->employee_code;
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
    public function do1Action()
    {
        $request = $this->getRequest();
        $context = $this->params()->fromQuery('context');

        // accepted only ajax request
        /*
         * if (! $request->isXmlHttpRequest ()) {
         * return $this->redirect ()->toRoute ( 'access_denied' );
         * }
         */
        $this->layout("layout/user/ajax");

        $q = $this->params()->fromQuery('q');

        if ($q !== "") {
            $results = $this->employeeSearchService->search($q);
        } else {
            $results = [
                "message" => "",
                "hits" => null,
                'context' => $context
            ];
        }

        // var_dump($results);
        return new ViewModel(array(
            'message' => $results["message"],
            'hits' => $results["hits"],
            'context' => $context
        ));
    }

    // SETTER AND GETTER
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    public function getEmployeeSearchService()
    {
        return $this->employeeSearchService;
    }

    public function setEmployeeSearchService(EmployeeSearchService $employeeSearchService)
    {
        $this->employeeSearchService = $employeeSearchService;
        return $this;
    }
}
