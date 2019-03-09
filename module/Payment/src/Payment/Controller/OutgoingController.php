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
                      
                     
                    break;
                case \Payment\Model\Constants::OUTGOING_PO:
                    
                    $errors = $this->apPaymentService->reverse($entity, $u, $reversalDate, $reversalReason);
                    break;
            }
            
            if(count($errors)>0){
                
                $m = $nmtPlugin->translate("Reversal failed!");
                $this->flashMessenger()->addMessage($m);
                
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
            
            $m = sprintf("Outgoing payment #%s reversed", $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            
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
            //$target_token = $data['target_token'];
            $isDraft = (int) $data['isDraft'];
            
            /*  $criteria = array(
             'id' => $target_id,
             'token' => $target_token
             ); */
            
             //$target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
            
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $entity_array = $res->getPo($target_id);
            
            if ($entity_array == null) {
                return $this->redirect()->toRoute('access_denied');
            }
            
            /**@var \Application\Entity\NmtProcurePo $target*/
            $target=$entity_array[0];
            
            if ($target == null) {
                
                $errors[] = 'PO object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'nmtPlugin' => $nmtPlugin,
                    'entity_array' => $entity_array,
                ));
                
                $viewModel->setTemplate("payment/outgoing/pay-po");
                return $viewModel;
            }
            
            $entity = new \Application\Entity\PmtOutgoing();
            $entity->setPo($target);
            $entity->setTargetId($target->getId());
            
            $entity->setLocalCurrency($default_cur);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            
            try {
                if ($isDraft == 1) {
                    $ck = $this->poPaymentService->saveHeader($entity, $data, $u, TRUE);
                    $m = sprintf('[OK] Payment #%s for PO #%s created', $entity->getId(), $target->getId());
                    
                } else {
                    $ck = $this->poPaymentService->post($entity, $data, $u, TRUE, TRUE);
                    $m = sprintf('[OK] Payment #%s for PO #%s posted', $entity->getSysNumber(), $target->getId());
                    
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
                    'nmtPlugin' => $nmtPlugin,
                    'entity_array' => $entity_array,
                ));
                
                $viewModel->setTemplate("payment/outgoing/pay-po");
                return $viewModel;
            }
            
            $this->flashMessenger()->addMessage($m);
            
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
       
       /*  $criteria = array(
            'id' => $id,
            'token' => $token
        ); */
        
        
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $entity_array = $res->getPo($id, $token);
        
        
        //$target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        
        
        
        if ($entity_array == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        /**@var \Application\Entity\NmtProcurePo $target*/
        $target = $entity_array[0];
        
        $entity = new \Application\Entity\PmtOutgoing();
        $entity->setIsActive(1);
        $entity->setVendor($target->getVendor());
        $entity->setDocAmount($entity_array['gross_amount']-$entity_array['billed_amount']);
        $entity->setPo($target);
        
        $entity->setDocCurrency($target->getCurrency());
        $entity->setRemarks(sprintf("Pay %s PO #%s", $target->getVendorName(), $target->getContractNo()));
        
        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
        
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'nmtPlugin' => $nmtPlugin,
            'entity_array' => $entity_array,
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
            
           /*  $criteria = array(
                'id' => $target_id,
                'token' => $target_token
            ); */

            /**@var \Application\Entity\FinVendorInvoice $target*/
            //$target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
            
            
            /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
            $ap = $res->getVendorInvoice($target_id);
            
            if ($ap == null) {
                return $this->redirect()->toRoute('access_denied');
            }
            
            $target=$ap[0];

            if ($target == null) {

                $errors[] = 'Target object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'nmtPlugin' => $nmtPlugin,
                    'ap' => $ap,
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
                    $m = sprintf('[OK] Payment #%s for AP #%s created', $entity->getId(), $target->getId());
                    
                } else {
                    $ck = $this->apPaymentService->post($entity, $data, $u, TRUE, TRUE);
                    $m = sprintf('[OK] Payment #%s for AP #%s posted', $entity->getSysNumber(), $target->getId());
                    
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
                    'nmtPlugin' => $nmtPlugin,
                    'ap' => $ap,
                    
                ));

                $viewModel->setTemplate("payment/outgoing/pay-ap");
                return $viewModel;
            }

            $this->flashMessenger()->addMessage($m);
         
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

        
        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $ap = $res->getVendorInvoice($id, $token);
        
        
        //$target = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findOneBy($criteria);
        
        

        if ($ap == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        /**@var \Application\Entity\FinVendorInvoice $target*/
        $target = $ap[0];

        $entity = new \Application\Entity\PmtOutgoing();
        $entity->setIsActive(1);
        $entity->setVendor($target->getVendor());
        $entity->setDocAmount($ap['gross_amount']-$ap['total_doc_amount_paid']);
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
            'nmtPlugin' => $nmtPlugin,
            'ap' => $ap,            
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
            $nTry = $data['n'] + 1;

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
                    'ap' =>null,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("payment/outgoing/pay-ap");
                return $viewModel;
            }
            
             
            /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
            $ap = $res->getVendorInvoice( $entity->getApInvoice()->getId(), $entity->getApInvoice()->getToken());
            $target = $ap[0];
            

            if ($isDraft == 1) {
                $ck = $this->apPaymentService->saveHeader($entity, $data, $u, FALSE);
                $m = sprintf('[OK] Payment #%s for AP %s updated', $entity->getId(),$entity->getApInvoice()->getId());
                
            } else {
                $ck = $this->apPaymentService->post($entity, $data, $u, FALSE, TRUE);
                $m = sprintf('[OK] Payment #%s for AP %s updated and posted', $entity->getSysNumber(),$entity->getApInvoice()->getId());
            }

            if (count($ck) > 0) {
                $errors = array_merge($errors, $ck);
            }
            
            
            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit "AP Payment. %s (%s)"?', $entity->getId(), $entity->getSysNumber());
            }
            
            if ($nTry == 5) {
                $m1 = sprintf('You might be not ready to edit "AP Payment %s (%s)". Please try later!', $entity->getId(), $entity->getSysNumber());
                $this->flashMessenger()->addMessage($m1);
                return $this->redirect()->toUrl($redirectUrl);
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
                    'ap' => $ap,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("payment/outgoing/pay-ap");
                return $viewModel;
            }

            $this->flashMessenger()->addMessage($m);
       
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
        //$target = $entity->getApInvoice();
        
        
        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $ap = $res->getVendorInvoice( $entity->getApInvoice()->getId(), $entity->getApInvoice()->getToken());
        $target = $ap[0];
        

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'nmtPlugin' => $nmtPlugin,
            'ap' => $ap,
            
            'n' => 0
        ));

        $viewModel->setTemplate("payment/outgoing/pay-ap");
        return $viewModel;
    }
    
    
    /**
     * Edit Pay PO
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editPayPOAction()
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
            $nTry = $data['n'] + 1;
            
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
                    'entity_array' =>null,
                    'n' => $nTry
                ));
                
                $viewModel->setTemplate("payment/outgoing/pay-ap");
                return $viewModel;
            }
            
            
            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $entity_array = $res->getPo($entity->getPo()->getId());
            
            
            $target = $entity_array[0];
            
            
            if ($isDraft == 1) {
                $ck = $this->poPaymentService->saveHeader($entity, $data, $u, FALSE);
                $m = sprintf('[OK] Payment #%s for PO %s updated', $entity->getId(),$entity->getPo()->getId());
                
            } else {
                $ck = $this->poPaymentService->post($entity, $data, $u, FALSE, TRUE);
                $m = sprintf('[OK] Payment #%s for PO %s updated and posted', $entity->getSysNumber(),$entity->getPo()->getId());
            }
            
            if (count($ck) > 0) {
                $errors = array_merge($errors, $ck);
            }
            
            
            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit "PO Payment. %s (%s)"?', $entity->getId(), $entity->getSysNumber());
            }
            
            if ($nTry == 5) {
                $m1 = sprintf('You might be not ready to edit "PO Payment %s (%s)". Please try later!', $entity->getId(), $entity->getSysNumber());
                $this->flashMessenger()->addMessage($m1);
                return $this->redirect()->toUrl($redirectUrl);
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
                    'entity_array' =>$entity_array,                    
                    'n' => $nTry
                ));
                
                $viewModel->setTemplate("payment/outgoing/pay-po");
                return $viewModel;
            }
            
            $this->flashMessenger()->addMessage($m);
            
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
        //$target = $entity->getApInvoice();
        
        
        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
              
        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $entity_array = $res->getPo($entity->getPo()->getId());
        
        
        if ($entity_array == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        
        $target = $entity_array[0];
        
        
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'nmtPlugin' => $nmtPlugin,
            'entity_array' => $entity_array,            
            'n' => 0
        ));
        
        $viewModel->setTemplate("payment/outgoing/pay-po");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
         
        $criteria = array(
        );
        $sort_criteria = array(
            'createdOn'=>'DESC'
        );
        
        $list = $this->doctrineEM->getRepository('\Application\Entity\PmtOutgoing')->findBy($criteria,$sort_criteria);
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
