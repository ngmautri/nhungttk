<?php
namespace Application\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Entity\NmtApplicationUom;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomController extends AbstractGenericController
{


    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function initAction()
    {}

    /**
     *
     * @version 3.0
     * @author Ngmautri
     *
     *         Create new Department
     */
    public function addAction()
    {
        $request = $this->getRequest();

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));
        if ($request->isPost()) {

            // $input->status = $request->getPost ( 'status' );
            // $input->remarks = $request->getPost ( 'description' );

            $uom_name = $request->getPost('uom_name');
            $uom_code = $request->getPost('uom_code');
            $uom_description = $request->getPost('uom_description');
            $status = $request->getPost('status');
            $converstion_factor = $request->getPost('converstion_factor');
            /*
             * $sector= $request->getPost ( 'sector' );
             * $symbol= $request->getPost ( 'symbol' );
             */
            $errors = array();

            if ($uom_name === '' or $uom_name === null) {
                $errors[] = 'Please give the name';
            }

            $r = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->findBy(array(
                'uomName' => $uom_name
            ));

            if (count($r) >= 1) {
                $errors[] = $uom_name . ' exists';
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors
                ));
            }

            // No Error

            $entity = new NmtApplicationUom();
            $entity->setUomName($uom_name);
            $entity->setUomCode($uom_code);
            $entity->setUomDescription($uom_description);
            $entity->setConversionFactor($converstion_factor);
            $entity->setStatus($status);
            $entity->setCreatedOn(new \DateTime());
            $entity->setCreatedBy($u);

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
        }

        /*
         * if ($request->isXmlHttpRequest ()) {
         * $this->layout ( "layout/inventory/ajax" );
         * }
         */
        return new ViewModel(array(
            'errors' => null
        ));
    }

    /**
     */
    public function listAction()
    {
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationUom')->findAll();
        $total_records = count($list);
        // $jsTree = $this->tree;
        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => null
        ));
    }

    /**
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
