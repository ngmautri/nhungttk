<?php
namespace Application\Controller;

use Application\Controller\Contracts\AbstractGenericController;
use Application\Service\ApplicationService;
use Application\Domain\Util\Pagination\Paginator;
use Zend\Math\Rand;
use Zend\View\Model\ViewModel;

/**
 *
 * @copyright 2018
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 * @since 19/6/2018
 * @version 1.0
 *         
 */
class AclController extends AbstractGenericController
{

    const CHAR_LIST = "_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

    protected $appService;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listAction()
    {
        $modules = $this->appService->getLoadedModules();

        // var_dump($modules);

        return new ViewModel(array(
            'modules' => $modules,
            'e' => $this->getEvent()
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function listResourcesAction()
    {
        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 18;
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

        $criteria = array(
            'isActive' => 1
        );
        $sort = array(
            'module' => 'ASC',
            'controller' => 'ASC',
            'action' => 'ASC'
        );

        $resources = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclResource')->findBy($criteria, $sort);
        $totalResults = count($resources);
        $paginator = null;

        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $resources = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclResource')->findBy($criteria, $sort, ($paginator->getMaxInPage() - $paginator->getMinInPage()) + 1, $paginator->getMinInPage() - 1);
        }

        return new ViewModel(array(
            'total_resources' => $totalResults,
            'resources' => $resources,
            'paginator' => $paginator
        ));
    }

    /**
     * changed 19/6
     *
     * @version: 1.0
     * @return \Zend\View\Model\ViewModel
     */
    public function updateSysResourcesAction()
    {
        $resources = $this->appService->getAppLoadedResources();

        /** @var \Application\Entity\MlaUsers $u */
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        // Set all resources as inactive;
        $q = $this->doctrineEM->createQuery("UPDATE Application\Entity\NmtApplicationAclResource res SET res.isActive = 0, res.currentState = ''");
        $q->execute();

        $new_counter = 0;
        $updated_counter = 0;

        // echo count($resources);

        foreach ($resources as $res) {

            /** @var \Application\Entity\NmtApplicationAclResource $saved_res */
            $saved_res = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclResource')->findOneBy(array(
                'resource' => $res['resource']
            ));

            if (! $saved_res instanceof \Application\Entity\NmtApplicationAclResource) {
                $new_counter ++;
                $input = new \Application\Entity\NmtApplicationAclResource();
                $input->setModule($res['module']);
                $input->setController($res['controller']);
                $input->setAction($res['action']);
                $input->setResource($res['resource']);
                $input->setCreatedOn(new \DateTime());
                $input->setType("ROUTE");
                $input->setIsActive(1);
                $input->setCurrentState("ACTIVE");
                $input->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));
                $input->setRemarks("created by " . $u->getFirstname() . " " . $u->getLastname());
                $input->setCreatedBy($u);
                $this->doctrineEM->persist($input);
            } else {
                $updated_counter ++;
                $saved_res->setIsActive(1);
                $saved_res->setCurrentState("ACTIVE");
                $saved_res->setChangeOn(new \DateTime());
                $saved_res->setRemarks("updated by " . $u->getFirstname() . " " . $u->getLastname());

                $saved_res->setChangeBy($u);
                $saved_res->setToken(Rand::getString(10, self::CHAR_LIST, true) . "_" . Rand::getString(21, self::CHAR_LIST, true));

                $this->doctrineEM->persist($saved_res);
            }
        }

        $this->doctrineEM->flush();

        return new ViewModel(array(
            'new_counter' => $new_counter,
            'updated_counter' => $updated_counter
        ));
    }

    /**
     *
     * @copyright nmt@mascot.dk
     * @deprecated 3/12/2017
     * @return \Zend\View\Model\ViewModel
     */
    public function updateResourcesAction()
    {
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);
        $resources = $this->appService->getAppLoadedResources();

        $counter = 0;
        foreach ($resources as $res) {
            $saved_res = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclResource')->findBy(array(
                'resource' => $res['resource']
            ));

            if ($saved_res == null) {
                $counter ++;
                $input = new \Application\Entity\NmtApplicationAclResource();
                $input->setModule($res['module']);
                $input->setController($res['controller']);
                $input->setController($res['controller']);
                $input->setAction($res['action']);
                $input->setResource($res['resource']);
                $input->setCreatedOn(new \DateTime());
                $input->setType("ROUTE");
                $input->setRemarks("created by " . $user['firstname'] . " " . $user['lastname']);
                $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $user['id']);
                $input->setCreatedBy($u);
                $this->doctrineEM->persist($input);
                $this->doctrineEM->flush();
            }

            /*
             * if(!$this->aclResourceTable->isResourceExits($res))
             * {
             * $input= new AclResource();
             * $input->resource = $res;
             * $input->type = "ROUTE";
             * $this->aclResourceTable->add($input);
             * }
             */
        }
        return new ViewModel(array(
            'counter' => $counter
        ));
    }

    // SETTER AND GETTER
    public function getAppService()
    {
        return $this->appService;
    }

    public function setAppService(ApplicationService $appService)
    {
        $this->appService = $appService;
        return $this;
    }
}
