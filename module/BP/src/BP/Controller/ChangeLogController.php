<?php
namespace BP\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Domain\Util\Pagination\Paginator;

/**
 * Item Change Log
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ChangeLogController extends AbstractActionController
{

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {}

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

        $object_token = $this->params()->fromQuery('object_token');
        $object_id = $this->params()->fromQuery('object_id');
        $class_name = $this->params()->fromQuery('class_name');

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 10;
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

        $criteria = array(
            'objectToken' => $object_token,
            'objectId' => $object_id
            // 'className' => $class_name
        );

        $sort_criteria = array(
            'fieldName' => 'ASC',
            'revisionNo' => 'DESC',
            'createdOn' => 'DESC'
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtBpChangeLog')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtBpChangeLog')->findBy($criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'objectToken' => $object_token,
            'objectId' => $object_id,
            'className' => $class_name
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
