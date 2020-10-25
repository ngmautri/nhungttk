<?php
namespace Application\Controller;

use Application\Controller\Contracts\CRUDController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomGroupController extends CRUDController
{

    public function __construct()
    {
        $this->setBaseUrl();
        $this->setAjaxLayout();
        $this->setDefaultLayout();
        $this->setListTemplate();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Controller\Contracts\CRUDController::setBaseUrl()
     */
    protected function setBaseUrl()
    {
        $this->baseUrl = '/application/uom-group';
    }

    protected function setAjaxLayout()
    {
        $this->ajaxLayout = "layout/user/ajax";
    }

    protected function setDefaultLayout()
    {
        $this->defaultLayout = "Application/layout-fluid";
    }

    protected function setListTemplate()
    {
        $this->listTemplate = $this->getBaseUrl() . '/list';
    }

    /**
     *
     * @deprecated
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();
        $context = $this->params()->fromQuery('context');

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->findBy(array(), array(
            'uomCode' => 'ASC'
        ));
        $total_records = count($list);

        $this->getResponse()
            ->getHeaders()
            ->addHeaderLine('Expires', '3800', true);
        $this->getResponse()
            ->getHeaders()
            ->addHeaderLine('Cache-Control', 'public', true);
        $this->getResponse()
            ->getHeaders()
            ->addHeaderLine('Cache-Control', 'max-age=3800');
        $this->getResponse()
            ->getHeaders()
            ->addHeaderLine('Pragma', '', true);

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null,
            'context' => $context
        ));
    }
}
