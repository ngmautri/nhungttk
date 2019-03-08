<?php
namespace Payment\Controller;

use Application\Entity\FinVendorInvoice;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class OutgoingController extends AbstractActionController
{

    protected $doctrineEM;

    protected $apPaymentService;

    protected $poPaymentService;

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function showAction()
    {
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
        /*
         * if ($this->getRequest()->getHeader('Referer') !== null) {
         * $redirectUrl = $this->getRequest()
         * ->getHeader('Referer')
         * ->getUri();
         * }
         */

        $id = (int) $this->params()->fromQuery('entity_id');
        $criteria = array(
            'id' => $id
        );

        /** @var \Application\Entity\PmtOutgoing $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\PmtOutgoing')->findOneBy($criteria);

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_SHOW,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => null,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("payment/outgoing/show");
        return $viewModel;
    }
    
    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function reverseAction()
    {
        $request = $this->getRequest();        
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        
        
        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));
        
        // Is Posing
        // =============================
        if ($request->isPost()) {
            
            $errors = array();
            
            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            
            $reversalDate = $data['reversalDate'];
            $reversalReason = $data['reversalReason'];
            
            $criteria = array(
                'id' => $entity_id,
                //'token' => $entity_token
            );
            
            
            /** @var \Application\Entity\PmtOutgoing $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\PmtOutgoing')->findOneBy($criteria);
            
            if ($entity == null) {
                return $this->redirect()->toRoute('access_denied');
            }
            
            $viewModel = new ViewModel(array(
                'action' => \Application\Model\Constants::FORM_ACTION_SHOW,
                'redirectUrl' => $redirectUrl,
                'errors' => null,
                'entity' => $entity,
                'target' => null,
                'nmtPlugin' => $nmtPlugin
            ));
            
            $doc_type = $entity->getDocType();
             
            switch ($doc_type) {
                
                case \Payment\Model\Constants::OUTGOING_AP:
                    $errors = $this->apPaymentService->reverse($entity, $u, $reversalDate, $reversalReason);
                    $m = count($errors);
                    $this->flashMessenger()->addMessage($m);
                    
                     
                    break;
                case \Payment\Model\Constants::OUTGOING_PO:
                    
                    $errors = $this->apPaymentService->reverse($entity, $u, $reversalDate, $reversalReason);
                    break;
            }
            
            if(count($errors)>0){
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_SHOW,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => null,
                    'nmtPlugin' => $nmtPlugin
                ));
                
                $viewModel->setTemplate("payment/outgoing/reverse");
                return $viewModel;
            }
            
            $redirectUrl = "/payment/outgoing/list";
            return $this->redirect()->toUrl($redirectUrl);
            
            
        }
        
        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
        /*
         * if ($this->getRequest()->getHeader('Referer') !== null) {
         * $redirectUrl = $this->getRequest()
         * ->getHeader('Referer')
         * ->getUri();
         * }
         */
        
        $id = (int) $this->params()->fromQuery('entity_id');
        $criteria = array(
            'id' => $id
        );
        
        /** @var \Application\Entity\PmtOutgoing $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\PmtOutgoing')->findOneBy($criteria);
        
        if ($entity == null) {
            //return $this->redirect()->toRoute('access_denied');
        }
        
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_SHOW,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => null,
            'nmtPlugin' => $nmtPlugin
        ));
        
        $viewModel->setTemplate("payment/outgoing/reverse");
        return $viewModel;
    }

    /**
     * Adding new vendor invoce
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $entity = new FinVendorInvoice();
            $entity->setLocalCurrency($default_cur);
            $entity->setTransactionType(\Application\Model\Constants::TRANSACTION_TYPE_PURCHASED);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $errors = $this->apService->validateHeader($entity, $data);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("finance/v-invoice/crud");
                return $viewModel;
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            try {
                $this->apService->saveHeader($entity, $u, TRUE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            // double check

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("finance/v-invoice/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] A/P Invoice #%s created', $entity->getId());
            $this->flashMessenger()->addMessage($m);

            $createdOn = new \DateTime();

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));

            // $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            $redirectUrl = "/finance/v-invoice-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();

            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $entity = new \Application\Entity\PmtOutgoing();
        $entity->setIsActive(1);

        // Default currency
        if ($default_cur instanceof \Application\Entity\NmtApplicationCurrency) {
            $entity->setDocCurrency($default_cur);
        }

        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("payment/outgoing/crud");
        return $viewModel;
    }

    /**
     * Pay PO
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function payPOAction()
    {
        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];
            $target_id = $data['target_id'];
            $target_token = $data['target_token'];

            $criteria = array(
                'id' => $target_id,
                'token' => $target_token
            );

            /**@var \Application\Entity\NmtProcurePo $target*/
            $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo')->findOneBy($criteria);

            if ($target == null) {

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("payment/outgoing/pay-po");
                return $viewModel;
            }

            $entity = new \Application\Entity\PmtOutgoing();
            $entity->setApInvoice($target);
            $entity->setTargetId($target->getId());

            $entity->setLocalCurrency($default_cur);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $errors = $this->poPaymentService->validateHeader($entity, $data, TRUE);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("payment/outgoing/pay-po");
                return $viewModel;
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            try {
                $this->poPaymentService->post($entity, $u, TRUE, TRUE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            // double check

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("payment/outgoing/pay-po");
                return $viewModel;
            }

            $m = sprintf('[OK] Payment for PO #%s created', $entity->getId());
            $this->flashMessenger()->addMessage($m);

            $createdOn = new \DateTime();

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));

            // $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            // $redirectUrl = "/payment/outgoing/list?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
            $redirectUrl = "/payment/outgoing/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**@var \Application\Entity\NmtProcurePo $target*/
        $target = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo')->findOneBy($criteria);

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = new \Application\Entity\PmtOutgoing();
        $entity->setIsActive(1);
        $entity->setVendor($target->getVendor());
        $entity->setDocAmount(1000);
        $entity->setTargetId($target->getId());

        $entity->setDocCurrency($target->getCurrency());
        $entity->setRemarks(sprintf("Pay %s PO #%s", $target->getVendorName(), $target->getContractNo()));

        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("payment/outgoing/pay-po");
        return $viewModel;
    }

    /**
     * Pay AP
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function payAPAction()
    {
        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];
            $target_id = $data['target_id'];
            $target_token = $data['target_token'];
            $isDraft = (int) $data['isDraft'];
            
            $criteria = array(
                'id' => $target_id,
                'token' => $target_token
            );

            /**@var \Application\Entity\FinVendorInvoice $target*/
            $target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);

            if ($target == null) {

                $errors[] = 'Target object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("payment/outgoing/pay-ap");
                return $viewModel;
            }

            $entity = new \Application\Entity\PmtOutgoing();
            $entity->setApInvoice($target);
            $entity->setTargetId($target->getId());

            $entity->setLocalCurrency($default_cur);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);

            try {
                if ($isDraft == 1) {
                    $ck = $this->apPaymentService->saveHeader($entity, $data, $u, TRUE);
                } else {
                    $ck = $this->apPaymentService->post($entity, $data, $u, TRUE);
                }

                if (count($ck) > 0) {
                    $errors = array_merge($errors, $ck);
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            // double check

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("payment/outgoing/pay-ap");
                return $viewModel;
            }

            $m = sprintf('[OK] A/P Invoice #%s created', $entity->getId());
            $this->flashMessenger()->addMessage($m);

            $createdOn = new \DateTime();

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));

            // $redirectUrl = "/finance/v-invoice/add1?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            // $redirectUrl = "/payment/outgoing/list?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
            $redirectUrl = "/payment/outgoing/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**@var \Application\Entity\FinVendorInvoice $target*/
        $target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = new \Application\Entity\PmtOutgoing();
        $entity->setIsActive(1);
        $entity->setVendor($target->getVendor());
        $entity->setDocAmount(1000);
        $entity->setApInvoice($target);

        $entity->setDocCurrency($target->getCurrency());
        $entity->setRemarks(sprintf("Pay %s Invoice #%s, %s", $target->getVendorName(), $target->getInvoiceNo(), $target->getSapDoc()));

        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("payment/outgoing/pay-ap");
        return $viewModel;
    }

    /**
     * Edit Pay AP
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editPayAPAction()
    {
        $request = $this->getRequest();
        $this->layout("Finance/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        // Is Posing
        // =============================
        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];
            $entity_id = $data['entity_id'];
            $entity_token = $data['entity_token'];
            $isDraft = (int) $data['isDraft'];
            $nTry = $data['n'];

            $criteria = array(
                'id' => $entity_id,
                'token' => $entity_token
            );

            /**@var \Application\Entity\PmtOutgoing $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\PmtOutgoing')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity PmtOutgoing not found!';
                $this->flashMessenger()->addMessage('Something wrong!');

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'nmtPlugin' => $nmtPlugin,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("payment/outgoing/pay-ap");
                return $viewModel;
            }

            if ($isDraft == 1) {
                $ck = $this->apPaymentService->saveHeader($entity, $data, $u, FALSE);
            } else {
                $ck = $this->apPaymentService->post($entity, $data, $u, FALSE, TRUE);
            }

            if (count($ck) > 0) {
                $errors = array_merge($errors, $ck);
            }

            // double check

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $entity->getApInvoice(),
                    'nmtPlugin' => $nmtPlugin,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("payment/outgoing/pay-ap");
                return $viewModel;
            }

            $m = sprintf('[OK] A/P Payment #%s created', $entity->getId());
            $this->flashMessenger()->addMessage($m);

            $createdOn = new \DateTime();

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('finance.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));

            $redirectUrl = "/payment/outgoing/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('entity_token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**@var \Application\Entity\PmtOutgoing $entity*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\PmtOutgoing')->findOneBy($criteria);
        $target = $entity->getApInvoice();

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'nmtPlugin' => $nmtPlugin,
            'n' => 0
        ));

        $viewModel->setTemplate("payment/outgoing/pay-ap");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $list = $this->doctrineEM->getRepository('\Application\Entity\PmtOutgoing')->findAll();
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
     * @return \Payment\Service\APPaymentService
     */
    public function getApPaymentService()
    {
        return $this->apPaymentService;
    }

    /**
     *
     * @param \Payment\Service\APPaymentService $apPaymentService
     */
    public function setApPaymentService(\Payment\Service\APPaymentService $apPaymentService)
    {
        $this->apPaymentService = $apPaymentService;
    }

    /**
     *
     * @return \Payment\Service\POPaymentService
     */
    public function getPoPaymentService()
    {
        return $this->poPaymentService;
    }

    /**
     *
     * @param \Payment\Service\POPaymentService $poPaymentService
     */
    public function setPoPaymentService(\Payment\Service\POPaymentService $poPaymentService)
    {
        $this->poPaymentService = $poPaymentService;
    }
}
