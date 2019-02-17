<?php
namespace Procure\Controller;

use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\Date;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Application\Entity\NmtProcureQoRow;
use Application\Entity\NmtProcureQo;

/**
 * Quotation
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QuoteController extends AbstractActionController
{

    protected $doctrineEM;

    protected $qoService;

    /**
     * Create new Quotation:
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addAction()
    {
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $incoterm_list = $nmtPlugin->incotermList();
        
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

            $entity = new NmtProcureQo();
            $entity->setLocalCurrency($default_cur);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $errors = $this->qoService->validateHeader($entity, $data);

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    
                    
                ));
            }

            // NO ERROR
            // Saving into DB
            // ==============================

            try {
                $this->qoService->saveHeader($entity, $u, TRUE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    
                ));
            }

            $createdOn = new \DateTime();
            $m = sprintf("[OK] Quotation: %s created!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));

            $redirectUrl = "/procure/quote-row/add?token=" . $entity->getToken() . "&target_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ....................
        // ================================
        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $entity = new NmtProcureQo();
        $entity->setIsActive(1);

        // Default currency
        if ($default_cur instanceof \Application\Entity\NmtApplicationCurrency) {
            $entity->setCurrency($default_cur);
        }
        $entity->setSysNumber(\Application\Model\Constants::SYS_NUMBER_UNASSIGNED);
        return new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list,
            'incoterm_list' => $incoterm_list,
            
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        $request = $this->getRequest();
        // $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $incoterm_list = $nmtPlugin->incotermList();
        

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $default_cur = null;
        if ($u->getCompany() instanceof \Application\Entity\NmtApplicationCompany) {
            $default_cur = $u->getCompany()->getDefaultCurrency();
        }

        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();

            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $id = (int) $data['entity_id'];
            $token = $data['token'];

            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po = $res->getQoute($id, $token);

            if ($po == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            /**@var \Application\Entity\NmtProcureQo $entity ;*/

            $entity = null;
            if ($po[0] instanceof NmtProcureQo) {
                $entity = $po[0];
            }

            if ($entity == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            // ========================

            if ($po['total_row'] == 0) {
                $m = sprintf('[INFO] Quotation #%s has no lines.', $entity->getId());
                $m1 = $nmtPlugin->translate('Document is incomplete!');
                $this->flashMessenger()->addMessage($m1);

                $redirectUrl = "/procure/quote/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
                return $this->redirect()->toUrl($redirectUrl);
            }

            if ($entity->getLocalCurrency() == null) {
                $entity->setLocalCurrency($default_cur);
            }

            // check for posting
            $errors = $this->qoService->validateHeader($entity, $data, TRUE);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_REVIEW,
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'currency_list' => $currency_list,
                    'total_row' => $po['total_row'],
                    'max_row_number' => $po['total_row'],
                    'net_amount' => $po['net_amount'],
                    'tax_amount' => $po['tax_amount'],
                    'gross_amount' => $po['gross_amount'],
                    'incoterm_list' => $incoterm_list,
                    
                ));

                $viewModel->setTemplate("procure/quote/show");
                return $viewModel;
            }

            // No ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $changeOn = new \DateTime();
            $oldEntity = clone ($entity);

            try {
                $this->qoService->post($entity, $u, true);
            } catch (\Exception $e) {

                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_REVIEW,
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'currency_list' => $currency_list,
                    'total_row' => $po['total_row'],
                    'max_row_number' => $po['total_row'],
                    'net_amount' => $po['net_amount'],
                    'tax_amount' => $po['tax_amount'],
                    'gross_amount' => $po['gross_amount'],
                    'incoterm_list' => $incoterm_list,
                    
                ));

                $viewModel->setTemplate("procure/quote/show");
                return $viewModel;
            }

            // LOGGING
            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

            $m = sprintf('[OK] Quotation %s - posted.', $entity->getSysNumber());

            // Trigger Change Log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.change.log', __METHOD__, array(
                'priority' => 7,
                'message' => $m,
                'objectId' => $entity->getId(),
                'objectToken' => $entity->getToken(),
                'changeArray' => $changeArray,
                'changeBy' => $u,
                'changeOn' => $changeOn,
                'revisionNumber' => $entity->getRevisionNo(),
                'changeDate' => $changeOn,
                'changeValidFrom' => $changeOn
            ));

            $this->flashMessenger()->addMessage($m);
            // $redirectUrl = "/finance/v-invoice/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            $redirectUrl = "/procure/quote/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate.....................
        // ==============================

        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') != null) {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getQoute($id, $token);

        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($po[0] instanceof NmtProcureQo) {
            $entity = $po[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
            $m = sprintf('[INFO] Quotaion #%s - %s already posted!', $entity->getId(), $entity->getSysNumber());
            $this->flashMessenger()->addMessage($m);
            $redirectUrl = "/procure/quote/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        if ($po['total_row'] == 0) {
            $m = sprintf('[INFO] Quotaion #%s has no lines.', $entity->getSysNumber());
            $m1 = $nmtPlugin->translate('Document is incomplete!');
            $this->flashMessenger()->addMessage($m1);
            $redirectUrl = "/procure/quote/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_REVIEW,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list,
            'incoterm_list' => $incoterm_list,
            
            'total_row' => $po['total_row'],
            'max_row_number' => $po['total_row'],
            'net_amount' => $po['net_amount'],
            'tax_amount' => $po['tax_amount'],
            'gross_amount' => $po['gross_amount'],
            'n' => 0
        ));

        $viewModel->setTemplate("procure/quote/show");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function downloadAction()
    {
        $request = $this->getRequest();
        if ($request->getHeader('Referer') == null) {
            //return $this->redirect()->toRoute('access_denied');
        }

        $format = $this->params()->fromQuery('format');
        $entity_id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        $criteria = array(
            'id' => $entity_id,
            'token' => $token
        );

        /**@var \Application\Entity\NmtProcureQo $entity*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);

        if ($entity == null) {
           // return $this->redirect()->toRoute('access_denied');
        }

        switch ($format) {
            case "xlsx":
                $downloadStrategy = new \Procure\Model\Qo\ExcelStrategy();
                break;
            case "ods":
                $downloadStrategy = new \Procure\Model\Qo\OdsStrategy();
                break;
        }

        $downloadStrategy->setDoctrineEM($this->doctrineEM);
        $downloadStrategy->doDownload($entity);
    }

    /**
     *
     * show
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $incoterm_list = $nmtPlugin->incotermList();
        

        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            // return $this->redirect()->toRoute('access_denied');
        }
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getQoute($id, $token);

        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = $po[0];

        if ($entity instanceof \Application\Entity\NmtProcureQo) {
            return new ViewModel(array(
                'action' => \Application\Model\Constants::FORM_ACTION_SHOW,
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'currency_list' => $currency_list,
                'incoterm_list' => $incoterm_list,
                
                'total_row' => $po['total_row'],
                'active_row' => $po['active_row'],
                'max_row_number' => $po['total_row'],
                'net_amount' => $po['net_amount'],
                'tax_amount' => $po['tax_amount'],
                'gross_amount' => $po['gross_amount']
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
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $incoterm_list = $nmtPlugin->incotermList();
        

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
            $entity_id = $data['entity_id'];
            $token = $data['entity_token'];
            $nTry = $data['n'];

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtProcureQo $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);

            if (! $entity instanceof \Application\Entity\NmtProcureQo) {

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                return new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    
                    'n' => $nTry
                ));
            }

            $oldEntity = clone ($entity);

            // validate and update entity
            $errors = $this->qoService->validateHeader($entity, $data);

            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);

            if (count($changeArray) == 0) {
                $nTry ++;
                $errors[] = sprintf('Nothing changed! n = %s', $nTry);
            }

            if ($nTry >= 3) {
                $errors[] = sprintf('Do you really want to edit "%s"?', $entity->getSysNumber());
            }

            if ($nTry == 5) {
                $m = sprintf('You might be not ready to edit "%s (%s)". Please try later!', $entity->getSysNumber());
                $this->flashMessenger()->addMessage($m);
                return $this->redirect()->toUrl($redirectUrl);
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    
                    'n' => $nTry
                ));

                $viewModel->setTemplate("procure/quote/add");
                return $viewModel;
            }

            // NO ERROR
            // Saving into DB................
            // ===============================

            $changeOn = new \DateTime();

            try {
                $this->qoService->saveHeader($entity, $u, FALSE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    
                    'n' => $nTry
                ));

                $viewModel->setTemplate("procure/quote/add");
                return $viewModel;
            }

            $m = sprintf('Quotation #%s - %s updated. Change No.:%s. OK!', $entity->getId(), $entity->getSysNumber(), count($changeArray));

            // Trigger Change Log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.change.log', __METHOD__, array(
                'priority' => 7,
                'message' => $m,
                'objectId' => $entity->getId(),
                'objectToken' => $entity->getToken(),
                'changeArray' => $changeArray,
                'changeBy' => $u,
                'changeOn' => $changeOn,
                'revisionNumber' => $entity->getRevisionNo(),
                'changeDate' => $changeOn,
                'changeValidFrom' => $changeOn
            ));

            // Trigger Activity Log . AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn
            ));

            $this->flashMessenger()->addMessage($m);
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate.................
        // ========================

        if ($this->getRequest()->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        } else {
            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        /**@var \Application\Entity\NmtProcureQo $entity*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQo')->findOneBy($criteria);

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list,
            'incoterm_list' => $incoterm_list,
            
            'n' => 0
        ));

        $viewModel->setTemplate("procure/quote/add");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function listAction()
    {
        $is_active = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');
        $docStatus = $this->params()->fromQuery('docStatus');
        
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

        $is_active = (int) $this->params()->fromQuery('is_active');

        if ($is_active == null) {
            $is_active = 1;
        }
        
        if ($docStatus == null) {
            $docStatus = 'posted';
            
            if ($sort_by == null) :
                $sort_by = "sysNumber";
            endif;
        }
        
        
        if ($docStatus == "draft") {
            
            if ($sort_by == null) :
                $sort_by = "createdOn";
            endif;
            
            if ($sort == null) :
                 $sort = "DESC";
            endif;
        }

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $list = $res->getQOList($is_active, $currentState, $docStatus, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getQOList($is_active, $currentState, $docStatus, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState,
            'docStatus' => $docStatus
        ));
    }

    /**
     *
     * @return \Zend\View\Helper\ViewModel
     */
    public function vendorAction()
    {
        $request = $this->getRequest();

        // accepted only ajax request

        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        $vendor_id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        $is_active = (int) $this->params()->fromQuery('is_active');
        $sort_by = $this->params()->fromQuery('sort_by');
        $sort = $this->params()->fromQuery('sort');
        $currentState = $this->params()->fromQuery('currentState');

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

        $is_active = (int) $this->params()->fromQuery('is_active');

        if ($is_active == null) {
            $is_active = 1;
        }

        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        /**@var \Application\Repository\FinVendorInvoiceRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice');
        $list = $res->getInvoicesOf($vendor_id, $is_active, $currentState, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getInvoicesOf($vendor_id, $is_active, $currentState, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator,
            'is_active' => $is_active,
            'sort_by' => $sort_by,
            'sort' => $sort,
            'per_pape' => $resultsPerPage,
            'currentState' => $currentState
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function updateTokenAction()
    {
        $criteria = array();
        $sort_criteria = array();

        $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria);

        if (count($list) > 0) {
            foreach ($list as $entity) {

                /**@var \Application\Entity\FinVendorInvoice $entity ;*/

                if ($entity->getVendor() !== null) {
                    $entity->setVendorName($entity->getVendor()
                        ->getVendorName());
                }

                if ($entity->getCurrency() !== null) {
                    $entity->setCurrencyIso3($entity->getCurrency()
                        ->getCurrency());
                }

                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
        }

        $this->doctrineEM->flush();

        return new ViewModel(array(
            'list' => $list
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
     * @return \Procure\Service\QoService
     */
    public function getQoService()
    {
        return $this->qoService;
    }

    /**
     *
     * @param \Procure\Service\QoService $qoService
     */
    public function setQoService(\Procure\Service\QoService $qoService)
    {
        $this->qoService = $qoService;
    }
}
