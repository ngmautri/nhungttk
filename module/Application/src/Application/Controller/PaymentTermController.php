<?php
namespace Application\Controller;

use Application\Entity\NmtApplicationIncoterms;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PaymentTermController extends AbstractActionController
{

    protected $paymentTermService;

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationIncoterms')->findAll();
        $total_records = count($list);
        // $jsTree = $this->tree;
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        // $this->layout("Application/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $request = $this->getRequest();

        // Is Posting .................
        // ============================
        if ($request->isPost()) {
            $errors = array();
            $data = $this->params()->fromPost();
            
            $redirectUrl = $data['redirectUrl'];
            

            $entity = new NmtApplicationIncoterms();
            
            try {
                $errors = $this->incotermService->validateHeader($entity, $data);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    ));

                $viewModel->setTemplate("application/incoterm/crud");
                return $viewModel;
            }
            ;

            // No ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));

            try {
                $this->incotermService->saveHeader($entity, $u, TRUE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            // second check.

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                     ));

                $viewModel->setTemplate("application/incoterm/crud");
                return $viewModel;
            }

            $redirectUrl = "/application/incoterm/list";
            $m = sprintf("[OK] Incoterm: %s created!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = Null;

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $entity = new NmtApplicationIncoterms();
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity
        ));

        $viewModel->setTemplate("application/incoterm/crud");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtFinPostingPeriod')->findOneBy($criteria);
        if ($entity !== null) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
     
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
    
        $request = $this->getRequest();

        // Is Posing
        // =============================

        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $entity_id = (int) $data['entity_id'];
            $nTry = $data['n'];

            $criteria = array(
                'id' => $entity_id,
            );

            /** @var \Application\Entity\NmtApplicationIncoterms $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationIncoterms')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("application/incoterm/crud");
                return $viewModel;
            }

            // entity found
    
            $oldEntity = clone ($entity);

            try {
                $errors = $this->incotermService->validateHeader($entity, $data);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            /**
             *
             * @todo: problem when both attribut is 0
             */
            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

            if (count($changeArray) == 0) {
                $nTry ++;
                $errors[] = sprintf('Nothing changed! n = %s', $nTry);
            }

            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit "Incoterm. %s"?', $entity->getId());
            }

            if ($nTry == 5) {
                $m = sprintf('You might be not ready to edit Incoterm (%s). Please try later!', $entity->getId());
                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }

            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("application/incoterm/crud");
                return $viewModel;
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $changeOn = new \DateTime();

            try {
                $this->incotermService->saveHeader($entity, $u, FALSE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("application/incoterm/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] Incoterm #%s updated. Change No.=%s.', $entity->getId(),count($changeArray));

            // Trigger Change Log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('application.change.log', __METHOD__, array(
                'priority' => 7,
                'message' => $m,                                                                            
                'objectId' => $entity->getId(),
                'objectToken' => null,
                'changeArray' => $changeArray,
                'changeBy' => $u,
                'changeOn' => $changeOn,
                'revisionNumber' => $entity->getRevisionNo(),
                'changeDate' => $changeOn,
                'changeValidFrom' => $changeOn
            ));

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('application.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => null
            ));                                                                      

            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/application/incoterm/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
       /*  if ($this->getRequest()->getHeader('Referer') !== null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        } */

        $id = (int) $this->params()->fromQuery('entity_id');
         $criteria = array(
            'id' => $id,
        );

        /** @var \Application\Entity\NmtApplicationIncoterms $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationIncoterms')->findOneBy($criteria);
        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'n' => 0
        ));

        $viewModel->setTemplate("application/incoterm/crud");
        return $viewModel;
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
     * @return \PM\Controller\IndexController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * @return \Application\Service\IncotermService
     */
    public function getPaymentTermService()
    {
        return $this->paymentTermService;
    }

    /**
     *
     * @param \Application\Service\IncotermService $incotermService
     */
    public function setPaymentTermService(\Application\Service\PaymentTermService $paymentTermService)
    {
        $this->paymentTermService = $paymentTermService;
    }
}
