<?php
namespace Application\Controller;

use Application\Application\Service\MessageStore\MessageQuery;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class MessageStoreController extends AbstractActionController
{

    protected $doctrineEM;

    protected $messageQuery;
    
    /*
     * Defaul Action
     */
    public function indexAction() {
    }

    /**
     *
     * @return \Application\Application\Service\MessageStore\MessageQuery
     */
    public function getMessageQuery()
    {
        return $this->messageQuery;
    }

    /**
     *
     * @param MessageQuery $messageQuery
     */
    public function setMessageQuery(MessageQuery $messageQuery)
    {
        $this->messageQuery = $messageQuery;
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

        $entity_token = $this->params()->fromQuery('entity_token');
        $entity_id = $this->params()->fromQuery('entity_id');

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

        $total_records = $this->getMessageQuery()->getTotalMessagesOf($entity_id, $entity_token);

        $limit = null;
        $offset = null;
        $paginator=null;
        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);

            $limit = ($paginator->maxInPage - $paginator->minInPage) + 1;
            $offset = $paginator->minInPage - 1;
        }

        $result = $this->getMessageQuery()->getMessagesOf($entity_id,$entity_token, null, null,$limit, $offset);

        return new ViewModel(array(
            'list' => $result,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'entity_token' => $entity_token,
            'entity_id' => $entity_id
        ));
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
}
