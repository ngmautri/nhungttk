<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace HR\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Application\Domain\Util\Pagination\Paginator;
use Application\Entity\NmtApplicationAttachment;
use Zend\Http\Headers;
use Zend\Validator\Date;
use Zend\Math\Rand;

/**
 *
 * @author nmt
 *        
 */
class PayrollController extends AbstractActionController
{

    const CHAR_LIST = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();
        $redirectUrl = null;

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect ()->toRoute ( 'access_denied' );
        } else {
            $redirectUrl = $request->getHeader('Referer')->getUri();
        }

        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $checksum = $this->params()->fromQuery('checksum');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $entity_id,
            'checksum' => $checksum,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findOneBy($criteria);

        if (! $entity == null) {

            /**
             *
             * @todo Update Target
             */
            $target = $entity->getEmployee();

            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'target' => $target,
                'entity' => $entity
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {}

    /**
     * Return attachment of a target
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
        ;

        $this->layout("layout/user/ajax");

        $target_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $target_id,
            'token' => $token
        );

        /**
         *
         * @todo : Change Target
         */
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtHrEmployee')->findOneBy($criteria);

        if ($target !== null) {

            /**
             *
             * @todo : Change Target
             */
            $criteria = array(
                'employee' => $target_id,
                'isActive' => 1,
                'markedForDeletion' => 0
            );

            $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAttachment')->findBy($criteria);
            $total_records = count($list);
            $paginator = null;

            return new ViewModel(array(
                'list' => $list,
                'total_records' => $total_records,
                'paginator' => $paginator,
                'target' => $target
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        /**
         *
         * @todo : update target
         */
        $query = 'SELECT e FROM Application\Entity\NmtApplicationAttachment e WHERE e.employee > :n';

        $list = $this->doctrineEM->createQuery($query)
            ->setParameter('n', 0)
            ->getResult();

        if (count($list) > 0) {
            foreach ($list as $entity) {
                /**
                 *
                 * @todo Update Targnet
                 */
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        $total_records = count($list);
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */

    /**
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
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
