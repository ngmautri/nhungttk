<?php
namespace Procure\Controller;

use Application\Notification;
use Application\Domain\Shared\DTOFactory;
use Application\Entity\MlaUsers;
use Application\Entity\NmtProcurePo;
use Application\Entity\NmtProcureQo;
use Doctrine\ORM\EntityManager;
use MLA\Paginator;
use Procure\Application\Command\PO\AddRowCmd;
use Procure\Application\Command\PO\AddRowCmdHandler;
use Procure\Application\Command\PO\CreateHeaderCmd;
use Procure\Application\Command\PO\CreateHeaderCmdHandler;
use Procure\Application\Command\PO\EditHeaderCmd;
use Procure\Application\Command\PO\EditHeaderCmdHandler;
use Procure\Application\Command\PO\UpdateRowCmd;
use Procure\Application\Command\PO\UpdateRowCmdHandler;
use Procure\Application\Command\PO\Options\PoCreateOptions;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Application\Reporting\PO\PoReporter;
use Procure\Application\Service\PO\POService;
use Procure\Domain\Exception\PoCreateException;
use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoController extends AbstractActionController
{

    protected $doctrineEM;

    protected $poService;

    protected $purchaseOrderService;

    protected $poSearchService;

    protected $poReporter;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {}

    /**
     * Make P/O from QO
     * case GR-IR
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function copyFromQoAction()
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

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $id = (int) $data['target_id'];
            $token = $data['target_token'];

            /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
            $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
            $po = $res->getQoute($id, $token);

            /**@var \Application\Entity\NmtProcureQo $target ;*/
            $target = null;

            if ($po != null) {
                if ($po[0] instanceof NmtProcureQo) {
                    $target = $po[0];
                }
            }

            if ($target == null) {
                $errors[] = 'Quotation can\'t be empty!';
                $this->flashMessenger()->addMessage('Something wrong!');
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_PO_FROM_QO,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'target' => null,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/po/crud");
                return $viewModel;
            }

            $entity = new NmtProcurePo();
            $entity->setLocalCurrency($default_cur);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $errors = $this->poService->validateHeader($entity, $data);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_PO_FROM_QO,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/po/crud");
                return $viewModel;
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            try {
                $this->poService->saveHeader($entity, $u, TRUE);
                $this->poService->copyFromQO($entity, $target, $u, true);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'target' => $target,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/po/crud");
                return $viewModel;
            }

            $m = sprintf("[OK] PP #%s created from QO #%s", $entity->getSysNumber(), $target->getSysNumber());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/procure/po/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate.....................
        // ==============================

        $redirectUrl = null;
        if ($request->getHeader('Referer') != null) {

            $redirectUrl = $this->getRequest()
                ->getHeader('Referer')
                ->getUri();
        }

        $id = (int) $this->params()->fromQuery('target_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $qo = $res->getQoute($id, $token);

        if ($qo == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        /**@var \Application\Entity\NmtProcurePo $target ;*/

        $target = null;
        if ($qo[0] instanceof NmtProcureQo) {
            $target = $qo[0];
        }

        if ($target == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = new NmtProcurePo();
        $entity->setVendor($target->getVendor());
        $entity->setCurrency($target->getCurrency());
        $entity->setExchangeRate($target->getExchangeRate());
        $entity->setContractDate(new \Datetime());
        $entity->setIncoterm2($target->getIncoterm2());
        $entity->setIncotermPlace($target->getIncotermPlace());

        $entity->setIsActive(1);
        $entity->setRemarks("Ref." . $target->getSysNumber());

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_PO_FROM_QO,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'target' => $target,
            'currency_list' => $currency_list,
            'incoterm_list' => $incoterm_list,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("procure/po/crud");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/create";
        $form_title = "Create PO";
        $action = \Application\Model\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/po/crudPO";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'dto' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        try {

            $data = $prg;

            /**
             *
             * @var \Application\Entity\MlaUsers $u ;
             * @var PoDTO $dto ;
             */
            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                'email' => $this->identity()
            ));
            $dto = DTOFactory::createDTOFromArray($data, new PoDTO());
            $userId = $u->getId();
            $companyId = $u->getCompany()->getId();

            $options = new PoCreateOptions($companyId, $userId, __METHOD__);
            $cmd = new CreateHeaderCmd($this->getDoctrineEM(), $dto, $options, new CreateHeaderCmdHandler());

            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (PoCreateException $e) {

            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'version' => null,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/po/add-row?token=%s&target_id=%s", $dto->getToken(), $dto->getId());

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function addRowAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/add-row";
        $form_title = "Add PO Row";
        $action = \Application\Model\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/po/crudPORow";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('token');
            $rootEntity = $this->purchaseOrderService->getPO($target_id, $target_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,

                'entity_id' => null,
                'entity_token' => null,
                'target_id' => $target_id,
                'target_token' => $target_token,

                'dto' => null,
                'rootDto' => $rootEntity->makeHeaderDTO(),
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $data = $prg;

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        /**
         *
         * @var PoRowDTO $dto ;
         */
        $dto = DTOFactory::createDTOFromArray($data, new PoRowDetailsDTO());
        // var_dump($dto);

        $userId = $u->getId();

        $target_id = $data['target_id'];
        $target_token = $data['target_token'];
        $version = $data['version'];

        $rootEntity = $this->purchaseOrderService->getPO($target_id, $target_token);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $options = [
            "rootEntity" => $rootEntity,
            "rootEntityId" => $target_id,
            "rootEntityToken" => $target_token,
            "version" => $version,
            "userId" => $userId,
            "trigger" => __METHOD__
        ];

        $cmd = new AddRowCmd($this->getDoctrineEM(), $dto, $options, new AddRowCmdHandler());

        try {
            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {

            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'entity_token' => null,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'dto' => $dto,
                'rootDto' => $rootEntity->makeHeaderDTO(),
                'version' => $rootEntity->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/po/add-row?target_id=%s&token=%s", $target_id, $target_token);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function updateRowAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/update-row";
        $form_title = "Update PO Row";
        $action = \Application\Model\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "/procure/po/crudPORow";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('entity_token');
            $target_id = (int) $this->params()->fromQuery('target_id');
            $target_token = $this->params()->fromQuery('token');

            $result = $this->purchaseOrderService->getPOofRow($target_id, $target_token, $entity_id, $entity_token);

            $rootDTO = null;
            $localDTO = null;

            if (isset($result["rootDTO"])) {
                $rootDTO = $result["rootDTO"];
            }

            if (isset($result["localDTO"])) {
                $localDTO = $result["localDTO"];
            }

            if (! $rootDTO instanceof PoDetailsDTO || ! $localDTO instanceof PORowDetailsDTO) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'version' => $rootDTO->getRevisionNo(),
                'rootDto' => $rootDTO,
                'dto' => $localDTO, // row
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        // Posting

        $data = $prg;

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        /**
         *
         * @var PoRowDTO $dto ;
         */
        $dto = DTOFactory::createDTOFromArray($data, new PORowDetailsDTO());

        $userId = $u->getId();

        $target_id = $data['target_id'];
        $target_token = $data['target_token'];
        $entity_id = $data['entity_id'];
        $entity_token = $data['entity_token'];
        $version = $data['version'];

        $result = $this->purchaseOrderService->getPOofRow($target_id, $target_token, $entity_id, $entity_token);

        $rootEntity = null;
        $localEntity = null;
        $rootDTO = null;
        $localDTO = null;

        if (isset($result["rootEntity"])) {
            $rootEntity = $result["rootEntity"];
        }

        if (isset($result["localEntity"])) {
            $localEntity = $result["localEntity"];
        }
        if (isset($result["rootDTO"])) {
            $rootDTO = $result["rootDTO"];
        }

        if (isset($result["localDTO"])) {
            $localDTO = $result["localDTO"];
        }

        if ($rootEntity == null || $localEntity == null || $rootDTO == null || $localDTO == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $options = [
            "rootEntity" => $rootEntity,
            "localEntity" => $localEntity,
            "entityId" => $entity_id,
            "entityToken" => $entity_token,
            "version" => $version,
            "userId" => $userId,
            "trigger" => __METHOD__
        ];

        $cmd = new UpdateRowCmd($this->getDoctrineEM(), $dto, $options, new UpdateRowCmdHandler());

        try {
            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {

            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'target_id' => $target_id,
                'target_token' => $target_token,
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'dto' => $dto,
                'rootDto' => $rootDTO,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/po/review1?entity_id=%s&token=%s", $target_id, $target_token);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function amendAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/create";
        $form_title = "Create PO";
        $action = \Application\Model\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/po/crudPO";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => null,
                'dto' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $data = $prg;

        /**@var MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $dto = DTOFactory::createDTOFromArray($data, new PoDTO());

        $userId = $u->getId();
        $companyId = $u->getCompany()->getId();

        $notification = $this->purchaseOrderService->createHeader($dto, $companyId, $userId, __METHOD__, true);
        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => null,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = "/procure/po/list";

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\Http\PhpEnvironment\Response|\Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function updateAction()
    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/update";
        $form_title = "Edit PO";
        $action = \Application\Model\Constants::FORM_ACTION_EDIT;
        $viewTemplete = "procure/po/crudPO";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $token = $this->params()->fromQuery('token');
            $dto = $this->purchaseOrderService->getPOHeaderById($entity_id, $token);

            if ($dto == null) {
                return $this->redirect()->toRoute('not_found');
            }

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $token,
                'dto' => $dto,
                'version' => $dto->getRevisionNo(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $data = $prg;

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $dto = DTOFactory::createDTOFromArray($data, new PoDTO());

        $userId = $u->getId();
        $entity_id = $data['entity_id'];
        $entity_token = $data['entity_token'];
        $version = $data['version'];

        $rootEntity = $this->purchaseOrderService->getPOHeaderById($entity_id, $entity_token);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $options = [
            "rootEntity" => $rootEntity,
            "rootEntityId" => $entity_id,
            "rootEntityToken" => $entity_token,
            "version" => $version,
            "userId" => $userId,
            "trigger" => __METHOD__
        ];

        $cmd = new EditHeaderCmd($this->getDoctrineEM(), $dto, $options, new EditHeaderCmdHandler());

        try {
            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {

            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'dto' => $dto,
                'version' => $rootEntity->getRevisionNo(), // get current version.
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = sprintf("/procure/po/view?entity_id=%s&token=%s", $entity_id, $entity_token);

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
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

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $entity = new NmtProcurePo();
            $entity->setLocalCurrency($default_cur);
            $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_DRAFT);
            $errors = $this->poService->validateHeader($entity, $data);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/po/add");
                return $viewModel;
            }

            // No ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            try {
                $this->poService->saveHeader($entity, $u, TRUE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    'nmtPlugin' => $nmtPlugin
                ));

                $viewModel->setTemplate("procure/po/crud");
                return $viewModel;
            }

            $m = sprintf("[OK] PO #%s created!", $entity->getId());
            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/procure/po/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
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

        $entity = new NmtProcurePo();

        $entity->setIsActive(1);
        $entity->setCurrency($default_cur);

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list,
            'incoterm_list' => $incoterm_list,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("procure/po/crud");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function reviewAction()
    {
        $this->layout("Procure/layout-fullscreen");

        $request = $this->getRequest();

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $currency_list = $nmtPlugin->currencyList();
        $incoterm_list = $nmtPlugin->incotermList();

        $form_action = "/procure/po/review";
        $form_title = "Review PO";
        $action = \Application\Model\Constants::FORM_ACTION_ADD;
        $viewTemplete = "procure/po/crudPO";

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');

        // Is Posting .................
        // ============================
        if ($request->isPost()) {

            $errors = array();
            $data = $this->params()->fromPost();
            $redirectUrl = $data['redirectUrl'];

            $id = (int) $data['entity_id'];
            $token = $data['token'];

            $po = $res->getPo($id, $token);

            if ($po == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            /**@var \Application\Entity\NmtProcurePo $entity ;*/
            $entity = null;
            if ($po[0] instanceof NmtProcurePo) {

                $entity = $po[0];
            }

            if ($entity == null) {
                return $this->redirect()->toRoute('access_denied');
            }

            $oldEntity = clone ($entity);
            $errors = $this->poService->validateHeader($entity, $data, TRUE);

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_REVIEW,
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,

                    'total_row' => $po['total_row'],
                    'active_row' => $po['active_row'],
                    'max_row_number' => $po['total_row'],
                    'net_amount' => $po['net_amount'],
                    'tax_amount' => $po['tax_amount'],
                    'gross_amount' => $po['gross_amount']
                ));

                $viewModel->setTemplate($form_action);
                return $viewModel;
            }

            // No ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $changeOn = new \DateTime();
            $oldEntity = clone ($entity);
            try {
                $this->poService->doPosting($entity, $u, true);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_REVIEW,
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,

                    'total_row' => $po['total_row'],
                    'active_row' => $po['active_row'],
                    'max_row_number' => $po['total_row'],
                    'net_amount' => $po['net_amount'],
                    'tax_amount' => $po['tax_amount'],
                    'gross_amount' => $po['gross_amount']
                ));

                $viewModel->setTemplate($form_action);
                return $viewModel;
            }

            // LOGGING
            $changeArray = $nmtPlugin->objectsAreIdentical($oldEntity, $entity);
            $m = sprintf('[OK] PO #%s posted.', $entity->getSysNumber());

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
            $redirectUrl = "/procure/po/list";
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');
        $po = $res->getPo($id, $token);

        if ($po == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $entity = null;
        if ($po[0] instanceof NmtProcurePo) {
            $entity = $po[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_REVIEW,

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

        $viewModel->setTemplate($form_action);
        return $viewModel;
    }

    /*
     * @return \Zend\View\Model\ViewModel
     */
    public function review1Action()

    {
        $this->layout("Procure/layout-fullscreen");

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $form_action = "/procure/po/review";
        $form_title = "Review PO";
        $action = \Application\Model\Constants::FORM_ACTION_REVIEW;
        $viewTemplete = "procure/po/review-v1";

        $prg = $this->prg($form_action, true);

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $entity_id = (int) $this->params()->fromQuery('entity_id');
            $entity_token = $this->params()->fromQuery('token');

            $rootEntity = $this->getPurchaseOrderService()->getPODetailsById($entity_id, $entity_token);

            if ($rootEntity == null) {
                return $this->redirect()->toRoute('not_found');
            }
            // echo memory_get_usage();
            // var_dump($po->makeDTOForGrid());
            // echo memory_get_usage();

            $viewModel = new ViewModel(array(
                'errors' => null,
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'entity_token' => $entity_token,
                'rootEntity' => $rootEntity,
                'rowOutput' => $rootEntity->getRowsOutput(),
                'headerDTO' => $rootEntity->makeDTOForGrid(),
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $data = $prg;

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $dto = DTOFactory::createDTOFromArray($data, new PoDTO());

        $userId = $u->getId();
        $entity_id = $data['entity_id'];

        $options = [
            "rootEntityId" => $entity_id,
            "userId" => $userId,
            "trigger" => __METHOD__
        ];

        $cmd = new EditHeaderCmd($this->getDoctrineEM(), $dto, $options, new EditHeaderCmdHandler());

        try {
            $cmd->execute();
            $notification = $dto->getNotification();
        } catch (\Exception $e) {

            $notification = new Notification();
            $notification->addError($e->getMessage());
        }

        if ($notification->hasErrors()) {
            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'redirectUrl' => null,
                'entity_id' => $entity_id,
                'dto' => $dto,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = "/procure/po/list";

        return $this->redirect()->toUrl($redirectUrl);
        // =======================
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function showAction()
    {
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $incoterm_list = $nmtPlugin->incotermList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $po = $res->getPo($id, $token);

        if ($po == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $entity = null;
        if ($po[0] instanceof NmtProcurePo) {
            $entity = $po[0];
        }

        if ($entity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        // echo memory_get_usage();

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_SHOW,
            'redirectUrl' => null,
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

        $viewModel->setTemplate("procure/po/review");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function viewAction()
    {
        $this->layout("Procure/layout-fullscreen");
        $request = $this->getRequest();

        /*
         * if ($request->getHeader('Referer') == null) {
         * return $this->redirect()->toRoute('not_found');
         * }
         */

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        $rootEntity = $this->getPurchaseOrderService()->getPODetailsById($id, $token);

        if ($rootEntity == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_SHOW,
            'form_action' => "/procure/po/view",
            'form_title' => $nmtPlugin->translate("Show PO"),
            'redirectUrl' => null,
            'rootEntity' => $rootEntity,
            'rowOutput' => $rootEntity->getRowsOutput(),
            'headerDTO' => $rootEntity->makeDTOForGrid(),
            'errors' => null,
            'nmtPlugin' => $nmtPlugin
        ));

        $viewModel->setTemplate("procure/po/review-v1");
        return $viewModel;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function statusAction()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $rows = $res->getPOStatus($id, $token);

        if ($rows == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        return new ViewModel(array(
            'rows' => $rows,
            'entity_id' => $id,
            'token' => $token
        ));
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function updateStatusAction()
    {
        $request = $this->getRequest();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo');
        $result = $res->updatePo($id, $token);

        $m = sprintf("[OK] PO #%s updated!", $id);
        $this->flashMessenger()->addMessage($m);

        $redirectUrl = sprintf("/procure/po/show?token=%s&entity_id=%s", $token, $id);
        return $this->redirect()->toUrl($redirectUrl);

        return new ViewModel(array(
            'result' => $result
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $this->layout("Procure/layout-fullscreen");

        /**@var Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();
        $currency_list = $nmtPlugin->currencyList();
        $incoterm_list = $nmtPlugin->incotermList();

        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            "email" => $this->identity()
        ));

        // Is Posting .................
        // ============================
        if ($request->isPost()) {
            $errors = array();
            $data = $this->params()->fromPost();

            $redirectUrl = $data['redirectUrl'];
            $entity_id = (int) $data['entity_id'];
            $token = $data['entity_token'];
            $nTry = $data['n'];

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtProcurePo $entity*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity object can\'t be empty. Or token key is not valid!';
                $this->flashMessenger()->addMessage('Something wrong!');
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => null,
                    'currency_list' => $currency_list,
                    'incoterm_list' => $incoterm_list,
                    'nmtPlugin' => $nmtPlugin,

                    'n' => $nTry
                ));

                $viewModel->setTemplate("procure/po/crud");
                return $viewModel;
            }

            // entity found
            $oldEntity = clone ($entity);

            $isPosted = FALSE;
            if ($entity->getDocStatus() == \Application\Model\Constants::DOC_STATUS_POSTED) {
                $isPosted = TRUE;
                $redirectUrl = "/procure/po/show?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            } else {
                $redirectUrl = "/procure/po/review?token=" . $entity->getToken() . "&entity_id=" . $entity->getId();
            }
            $errors = $this->poService->validateHeader($entity, $data, FALSE, $isPosted);

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
                $errors[] = sprintf('Do you really want to edit "AP Row. %s"?', $entity->getRowIdentifer());
            }

            if ($nTry == 5) {
                $m = sprintf('You might be not ready to edit AP Row (%s). Please try later!', $entity->getRowIdentifer());
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
                    'nmtPlugin' => $nmtPlugin,

                    'n' => $nTry
                ));

                $viewModel->setTemplate("procure/po/crud");
                return $viewModel;
            }

            // NO ERROR
            // Saving into Database..........
            // ++++++++++++++++++++++++++++++

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            $changeOn = new \DateTime();

            try {
                $this->poService->saveHeader($entity, $u);
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
                    'nmtPlugin' => $nmtPlugin,

                    'n' => $nTry
                ));

                $viewModel->setTemplate("procure/po/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] PO #%s - %s  updated. Change No.=%s.', $entity->getId(), $entity->getSysNumber(), count($changeArray));

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

            // Trigger: finance.activity.log. AbtractController is EventManagerAware.
            $this->getEventManager()->trigger('procure.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $changeOn,
                'entity_id' => $entity->getId(),
                'entity_class' => get_class($entity),
                'entity_token' => $entity->getToken()
            ));

            $this->flashMessenger()->addMessage($m);
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // Initiate ......................
        // ================================

        $redirectUrl = null;
        if ($this->getRequest()->getHeader('Referer') !== null) {
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

        /**@var \Application\Entity\NmtProcurePo $entity*/
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtProcurePo')->findOneBy($criteria);

        if ($entity == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_EDIT,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity,
            'currency_list' => $currency_list,
            'incoterm_list' => $incoterm_list,
            'nmtPlugin' => $nmtPlugin,

            'n' => 0
        ));

        $viewModel->setTemplate("procure/po/crud");
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

        if ($docStatus == null) :
            $docStatus = "posted";

            if ($sort_by == null) :
                $sort_by = "sysNumber";
            endif;            
        endif;


        if ($sort_by == null) :
            $sort_by = "createdOn";
        endif;

        if ($sort == null) :
            $sort = "DESC";
        endif;

        $list = $this->getPoReporter()->getPoList($is_active, $currentState, $docStatus, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $this->getPoReporter()->getPoList($is_active, $currentState, $docStatus, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        $viewModel = new ViewModel(array(
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

        $viewModel->setTemplate("procure/po/dto_list");
        return $viewModel;
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

        /**@var \Application\Repository\NmtProcurePoRepository $res ;*/
        $res = $this->doctrineEM->getRepository('\Application\Entity\NmtProcurePo');
        $list = $res->getPoOf($vendor_id, $is_active, $currentState, null, $sort_by, $sort, 0, 0);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            // $list = $this->doctrineEM->getRepository('Application\Entity\FinVendorInvoice')->findBy($criteria, $sort_criteria, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
            $list = $res->getPoOf($vendor_id, $is_active, $currentState, null, $sort_by, $sort, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
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

                $entity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
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
     * @return \Procure\Service\PoService
     */
    public function getPoService()
    {
        return $this->poService;
    }

    /**
     *
     * @param \Procure\Service\PoService $poService
     */
    public function setPoService(\Procure\Service\PoService $poService)
    {
        $this->poService = $poService;
    }

    /**
     *
     * @return \Procure\Service\PoService
     */
    public function getPoSearchService()
    {
        return $this->poSearchService;
    }

    /**
     *
     * @param \Procure\Service\PoService $poSearchService
     */
    public function setPoSearchService(\Procure\Service\PoSearchService $poSearchService)
    {
        $this->poSearchService = $poSearchService;
    }

    /**
     *
     * @return \Procure\Application\Service\PO\POService
     */
    public function getPurchaseOrderService()
    {
        return $this->purchaseOrderService;
    }

    /**
     *
     * @param POService $purchaseOrderService
     */
    public function setPurchaseOrderService(POService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

    /**
     *
     * @return \Procure\Application\Reporting\PO\PoReporter
     */
    public function getPoReporter()
    {
        return $this->poReporter;
    }

    /**
     *
     * @param PoReporter $poReporter
     */
    public function setPoReporter(PoReporter $poReporter)
    {
        $this->poReporter = $poReporter;
    }
}
