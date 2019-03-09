<?php
namespace Application\Controller;

use Application\Entity\NmtApplicationPmtTerm;
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
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->findAll();
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

            $entity = new NmtApplicationPmtTerm();

            try {
                $errors = $this->paymentTermService->saveHeader($entity, $data, $u, TRUE);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }

            if (count($errors) > 0) {
                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_ADD,

                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity
                ));

                $viewModel->setTemplate("application/payment-term/crud");
                return $viewModel;
            }
            ;

            $redirectUrl = "/application/payment-term/list";
            $m = sprintf("[OK] Payment Term #%s created!", $entity->getId());
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

        $entity = new NmtApplicationPmtTerm();
        $viewModel = new ViewModel(array(
            'action' => \Application\Model\Constants::FORM_ACTION_ADD,

            'redirectUrl' => $redirectUrl,
            'errors' => null,
            'entity' => $entity
        ));

        $viewModel->setTemplate("application/payment-term/crud");
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
            $entity_id = (int) $data['entity_id'];
            $nTry = $data['n'] + 1;

            $criteria = array(
                'id' => $entity_id
            );

            /** @var \Application\Entity\NmtApplicationPmtTerm $entity ; */
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->findOneBy($criteria);

            if ($entity == null) {

                $errors[] = 'Entity Payment Term not found.';
                $this->flashMessenger()->addMessage('Something wrong!');

                $viewModel = new ViewModel(array(
                    'action' => \Application\Model\Constants::FORM_ACTION_EDIT,
                    'redirectUrl' => $redirectUrl,
                    'errors' => $errors,
                    'entity' => $entity,
                    'n' => $nTry
                ));

                $viewModel->setTemplate("application/payment-term/crud");
                return $viewModel;
            }

            try {
                $errors = $this->paymentTermService->saveHeader($entity, $data, $u, FALSE);
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

                $viewModel->setTemplate("application/payment-term/crud");
                return $viewModel;
            }

            $m = sprintf('[OK] Payment Term #%s updated.', $entity->getId());

            $this->flashMessenger()->addMessage($m);

            $redirectUrl = "/application/payment-term/list";
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

        /** @var \Application\Entity\NmtApplicationPmtTerm $entity ; */
        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationPmtTerm')->findOneBy($criteria);
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

        $viewModel->setTemplate("application/payment-term/crud");
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
