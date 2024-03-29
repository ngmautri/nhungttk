<?php
namespace Inventory\Controller;

use Application\Domain\Util\Pagination\Paginator;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Activity Change Log
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ActivityLogController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $request = $this->getRequest();

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;

        return new ViewModel(array());
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 15;
        } else {
            $resultsPerPage = $this->params()->fromQuery('perPage');
        }
        ;

        if (is_null($this->params()->fromQuery('page'))) {
            $page = 1;
        } else {
            $page = $this->params()->fromQuery('page');
        }
        ;

        $criteria = array();

        $sort_criteria = array(
            'createdOn' => 'DESC'
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryLog')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        $limit = null;
        $offset = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $limit = $paginator->getLimit();
            $offset =$paginator->getOffset();
        }

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryLog')->findBy($criteria, $sort_criteria, $limit, $offset);

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
        ));
    }

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
