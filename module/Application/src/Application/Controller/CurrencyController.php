<?php
namespace Application\Controller;

use Application\Entity\NmtApplicationCurrency;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CurrencyController extends AbstractActionController
{

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

            $currency = $request->getPost('currency');
            $currency_numeric_code = $request->getPost('currency_numeric_code');
            $description = $request->getPost('description');
            $currency_entity = $request->getPost('entity');
            $status = $request->getPost('status');

            $errors = array();

            if ($currency === '' or $currency === null) {
                $errors[] = 'Please give the name!';
            }

            $r = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy(array(
                'currency' => $currency
            ));

            if (count($r) >= 1) {
                $errors[] = $currency . ' exists';
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors
                ));
            }

            // No Error
            $entity = new NmtApplicationCurrency();

            $entity->setCurrency($currency);
            $entity->setCurrencyNumericCode($currency_numeric_code);
            $entity->setStatus($status);
            $entity->setEntity($currency_entity);
            $entity->setDescription($description);

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
        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findAll();
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
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        // $jsTree = $this->tree;

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
            'paginator' => null
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function selectAction()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");
        $criteria = array(
            'isActive' => 1
        );
        $sort_criteria = array(
            'currency' => 'ASC'
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        // $jsTree = $this->tree;

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
            'paginator' => null
        ));
    }

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
