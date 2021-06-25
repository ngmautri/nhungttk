<?php
namespace Inventory\Controller;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Options\CmdOptions;
use Application\Controller\Contracts\AbstractGenericController;
use Application\Domain\Contracts\FormActions;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Inventory\Application\Command\TransactionalCommandHandler;
use Inventory\Application\Command\Item\Variant\CreateVariantCmdHandler;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemComponentController extends AbstractGenericController
{

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function createAction()
    {
        $this->layout('Inventory/layout-fluid');

        $form_action = "/inventory/item-component/create";
        $form_title = "Create Form";
        $action = FormActions::ADD;
        $viewTemplete = "/inventory/item-component/crud";

        /**@var \Application\Controller\Plugin\NmtPlugin $nmtPlugin ;*/
        $nmtPlugin = $this->Nmtplugin();

        $prg = $this->prg($form_action, true);
        $rep = new CompanyQueryRepositoryImpl($this->getDoctrineEM());
        $company = $rep->getById($this->getCompanyId());
        $attributeCollection = $company->getLazyItemAttributeGroupCollection();

        if ($prg instanceof \Zend\Http\PhpEnvironment\Response) {
            // returned a response to redirect us
            return $prg;
        } elseif ($prg === false) {
            // this wasn't a POST request, but there were no params in the flash messenger
            // probably this is the first time the form was loaded

            $itemId = $this->params()->fromQuery('item_id');

            $viewModel = new ViewModel(array(
                'errors' => null,
                'itemId' => $itemId,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO(),
                'attributeCollection' => $attributeCollection,
                'rootEntity' => null
            ));

            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        /*
         * |=============================
         * | POSTING
         * |
         * |=============================
         */
        $notification = null;
        try {
            $data = $prg;
            $itemId = $data['item_id'];

            $options = new CmdOptions($this->getCompanyVO(), $this->getUserId(), __METHOD__);
            $cmdHandler = new CreateVariantCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->getDoctrineEM(), $data, $options, $cmdHandlerDecorator, $this->getEventBusService());
            $cmd->setLogger($this->getLogger());
            $cmd->execute();
        } catch (\Exception $e) {
            $this->logInfo($e->getMessage());
        }

        $notification = $cmd->getNotification();

        if ($notification->hasErrors()) {

            $viewModel = new ViewModel(array(
                'errors' => $notification->getErrors(),
                'itemId' => $itemId,
                'redirectUrl' => null,
                'version' => null,
                'nmtPlugin' => $nmtPlugin,
                'form_action' => $form_action,
                'form_title' => $form_title,
                'action' => $action,
                'sharedCollection' => $this->getSharedCollection(),
                'companyVO' => $this->getCompanyVO(),
                'attributeCollection' => $attributeCollection,
                'rootEntity' => null
            ));
            $viewModel->setTemplate($viewTemplete);
            return $viewModel;
        }

        $this->flashMessenger()->addMessage($notification->successMessage(false));
        $redirectUrl = "/inventory/item/list2";

        return $this->redirect()->toUrl($redirectUrl);
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel|\Zend\Http\Response
     */
    public function updateAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        // $this->layout($this->getDefaultLayout());
        $this->layout("layout/user/ajax");
        $request = $this->getRequest();
        $itemId = $this->params()->fromQuery('target_id');

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('not_found');
        }

        $rep = new ItemQueryRepositoryImpl($this->getDoctrineEM());
        $item = $rep->getRootEntityById($itemId);

        if ($item == null) {
            // return $this->redirect()->toRoute('not_found');
        }

        $viewModel = new ViewModel(array(
            'item_id' => $itemId,
            'variantCollection' => $item->getLazyVariantCollection()
        ));

        return $viewModel;
    }
}
