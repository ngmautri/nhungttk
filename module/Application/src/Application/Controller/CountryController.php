<?php
namespace Application\Controller;

use Zend\Math\Rand;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Service\DepartmentService;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtApplicationAclUserRole;
use Application\Entity\NmtApplicationDepartment;
use Application\Entity\NmtApplicationCountry;
use Application\Domain\Util\Pagination\Paginator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CountryController extends AbstractActionController
{

    const CHAR_LIST = "__0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ__";

    protected $doctrineEM;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function initAction()
    {
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);
        $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $user['id']);

        $status = "initial...";

        // create ROOT NODE
        $e = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationDepartment')->findBy(array(
            'nodeName' => self::ROOT_NODE
        ));
        if (count($e) == 0) {
            // create super admin

            $input = new NmtApplicationDepartment();
            $input->setNodeName(self::ROOT_NODE);
            $input->setPathDepth(1);
            $input->setRemarks('Node Root');
            $input->setNodeCreatedBy($u);
            $input->setNodeCreatedOn(new \DateTime());
            $this->doctrineEM->persist($input);
            $this->doctrineEM->flush($input);
            $root_id = $input->getNodeId();
            $root_node = $this->doctrineEM->find('Application\Entity\NmtApplicationDepartment', $root_id);
            $root_node->setPath($root_id . '/');
            $this->doctrineEM->flush();
            $status = 'Root node has been created successfully: ' . $root_id;
        } else {
            $status = 'Root node has been created already.';
        }
        return new ViewModel(array(
            'status' => $status
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $country_name = $request->getPost('country_name');
            $country_code_2 = $request->getPost('country_code_2');
            $country_code_3 = $request->getPost('country_code_3');
            $country_numeric_code = $request->getPost('country_numeric_code');
            $status = $request->getPost('status');

            $errors = array();

            if ($country_name === '' or $country_name === null) {
                $errors[] = 'Please give the name of country';
            }

            $r = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findBy(array(
                'countryName' => $country_name
            ));

            if (count($r) >= 1) {
                $errors[] = $country_name . ' exists';
            }

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors
                ));
            }

            // No Error
            // +++++++++++++++++++++++++++++++

            $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
                "email" => $this->identity()
            ));
            $createdOn = new \DateTime();

            $entity = new NmtApplicationCountry();
            $entity->setCountryName($country_name);
            $entity->setCountryCode2($country_code_2);
            $entity->setCountryCode3($country_code_3);
            $entity->setCountryNumericCode($country_numeric_code);
            $entity->setCreatedOn($createdOn);
            $entity->setCreatedBy($u);
            $entity->setStatus($status);
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
        }

        return new ViewModel(array(
            'errors' => null
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $errors = array();

            $redirectUrl = $request->getPost('redirectUrl');
            $entity_id = (int) $request->getPost('entity_id');
            $token = $request->getPost('token');
            $nTry = $request->getPost('n');

            if ($token == "") {
                $token = null;
            }

            $criteria = array(
                'id' => $entity_id,
                'token' => $token
            );

            /**@var \Application\Entity\NmtApplicationCountry $entity ;*/
            $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findOneBy($criteria);

            if (! $entity instanceof \Application\Entity\NmtApplicationCountry) {
                $errors[] = "Entity not found!";

                return new ViewModel(array(
                    'redirectUrl' => $redirectUrl,
                    'entity' => $entity,
                    'errors' => $errors,
                    'n' => $nTry
                ));
            }

            // No Error
            // +++++++++++++++++++++++++++++++

            /*
             * $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
             * "email" => $this->identity()
             * ));
             */

            $isActive = (int) $request->getPost('isActive');
            if ($isActive != 1) {
                $isActive = 0;
            }

            $entity->setIsActive($isActive);

            if ($entity->getToken() == null) {
                $entity->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
            }
            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();

            $m = sprintf('Country (%s) updated. OK!', $entity->getCountryName());
            $this->flashMessenger()->addMessage($m);
            return $this->redirect()->toUrl($redirectUrl);
        }

        // NO POST
        // ===========================

        $redirectUrl = null;
        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $id = (int) $this->params()->fromQuery('entity_id');
        $token = $this->params()->fromQuery('token');

        if ($token == "") {
            $token = null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $entity = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findOneBy($criteria);
        if ($entity instanceof \Application\Entity\NmtApplicationCountry) {
            return new ViewModel(array(
                'redirectUrl' => $redirectUrl,
                'entity' => $entity,
                'errors' => null,
                'n' => 0
            ));
        } else {
            return $this->redirect()->toRoute('access_denied');
        }
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $criteria = array();
        $sort_criteria = array(
            "isActive" => "DESC",

            "countryName" => "ASC"
        );

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 20;
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

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        $paginator = null;

        if ($total_records > $resultsPerPage) {
            $paginator = new Paginator($total_records, $page, $resultsPerPage);
            $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findBy($criteria, $sort_criteria, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        }

        return new ViewModel(array(
            'list' => $list,
            'total_records' => $total_records,
            'paginator' => $paginator
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
            "isActive" => 1
        );
        $sort_criteria = array(
            "countryName" => "ASC"
        );

        $list = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCountry')->findBy($criteria, $sort_criteria);
        $total_records = count($list);
        // $jsTree = $this->tree;
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

    public function getDepartmentService()
    {
        return $this->departmentService;
    }

    public function setDepartmentService(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
        return $this;
    }
}
