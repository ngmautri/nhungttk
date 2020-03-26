<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\AclRoleTable;
use MLA\Paginator;
use Application\Service\AclService;
use Application\Entity\NmtApplicationAclRole;
use Doctrine\ORM\EntityManager;
use Application\Entity\NmtApplicationAclUserRole;
use User\Model\UserTable;
use Application\Entity\NmtApplicationAclRoleResource;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RoleController extends AbstractActionController
{

    const SUPER_ADMIN = 'super-administrator';

    const ADMIN = 'administrator';

    const MEMBER = 'member';

    protected $SmtpTransportService;

    protected $authService;

    protected $aclService;

    protected $userTable;

    protected $aclRoleTable;

    protected $tree;

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     */
    public function initAction()
    {
        $identity = $this->authService->getIdentity();
        $user = $this->userTable->getUserByEmail($identity);
        $u = $this->doctrineEM->find('Application\Entity\MlaUsers', $user['id']);

        // create super-administrator
        $e = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findBy(array(
            'role' => 'super-administrator'
        ));
        if (count($e) == 0) {
            // create super admin

            $input = new NmtApplicationAclRole();
            $input->setRole(self::SUPER_ADMIN);
            $input->setStatus("activated");
            $input->setPathDepth(1);
            $input->setRemarks("default role");
            $input->setCreatedBy($u);
            $input->setCreatedOn(new \DateTime());
            $this->doctrineEM->persist($input);
            $this->doctrineEM->flush($input);
            $supper_admin_id = $input->getId();
            $super_admin_role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $supper_admin_id);
            $super_admin_role->setPath($supper_admin_id . '/');
            $this->doctrineEM->flush();

            // create admin
            $input = new NmtApplicationAclRole();
            $input->setRole(self::ADMIN);
            $input->setStatus("activated");
            $input->setParentId($supper_admin_id);
            $input->setPathDepth(2);
            $input->setRemarks("default role");
            $input->setCreatedBy($u);
            $input->setCreatedOn(new \DateTime());
            $this->doctrineEM->persist($input);
            $this->doctrineEM->flush($input);
            $new_id = $input->getId();
            echo $new_id;

            $new_role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $new_id);
            $new_role->setPath($super_admin_role->getPath() . $new_id . '/');
            $this->doctrineEM->flush();
        }

        // create member
        $e = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findBy(array(
            'role' => self::MEMBER
        ));
        if (count($e) == 0) {

            $input = new NmtApplicationAclRole();
            $input->setRole(self::MEMBER);
            $input->setStatus("activated");
            $input->setPathDepth(1);
            $input->setRemarks("default role");
            $input->setCreatedBy($u);
            $input->setCreatedOn(new \DateTime());
            $this->doctrineEM->persist($input);
            $this->doctrineEM->flush($input);
            $member_id = $input->getId();
            $member_role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $member_id);
            $member_role->setPath($member_id . '/');
            $this->doctrineEM->flush();
        }
    }

    /**
     *
     * @version 3.0
     * @author Ngmautri
     *        
     *         create New Role
     */
    public function addAction()
    {
        $request = $this->getRequest();

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        $parent_id = (int) $this->params()->fromQuery('parent_id');

        if ($request->isPost()) {

            // $input->status = $request->getPost ( 'status' );
            // $input->remarks = $request->getPost ( 'description' );

            $role_name = $request->getPost('role');

            $errors = array();

            if ($role_name === '' or $role_name === null) {
                $errors[] = 'Please give role name';
            }

            $r = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findBy(array(
                'role' => $role_name
            ));
            /*
             * if (count($r)>=1) {
             * $errors [] = $role_name . ' exists';
             * }
             */

            $response = $this->getResponse();

            if (count($errors) > 0) {
                return new ViewModel(array(
                    'errors' => $errors,
                    'roles' => null,
                    'parent_id' => null
                ));
            }
            // Now Error
            $parent_id = $request->getPost('parent_id');
            $parent_entity = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $parent_id);
            // var_dump($parent_entity->getPath());

            $entity = new NmtApplicationAclRole();
            $entity->setRole($role_name);
            $entity->setParentId($parent_entity->getId());
            $entity->setCreatedOn(new \Datetime());
            $entity->setCreatedBy($u);
            $entity->setStatus("activated");
            $entity->setRemarks('done');

            $this->doctrineEM->persist($entity);
            $this->doctrineEM->flush();
            $new_id = $entity->getId();
            // var_dump($new_id);

            $new_entity = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $new_id);
            $new_entity->setPath($parent_entity->getPath() . $new_id . '/');

            $a = explode('/', $new_entity->getPath());
            $new_entity->setPathDepth(count($a) - 1);

            $this->doctrineEM->flush();
        }

        $roles = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRole')->findAll();
        /*
         * if ($request->isXmlHttpRequest ()) {
         * $this->layout ( "layout/inventory/ajax" );
         * }
         */
        return new ViewModel(array(
            'errors' => null,
            'roles' => $roles,
            'parent_id' => $parent_id
        ));
    }

    /**
     */
    public function listAction()
    {
        $data = $this->aclService->returnAclTree1();

        $this->tree($data, 'Super-administrator');
        $jsTree = $this->tree;
        return new ViewModel(array(
            'jsTree' => $jsTree
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function list1Action()
    {
        $roles = $this->aclService->returnAclTree();

        return new ViewModel(array(
            'roles' => $roles
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function addMemberAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        if ($request->isPost()) {

            $role_id = (int) $request->getPost('id');
            $user_id_list = $request->getPost('users');

            if (count($user_id_list) > 0) {
                foreach ($user_id_list as $member_id) {

                    /*
                     * $member = new AclUserRole ();
                     * $member->role_id = $role_id;
                     * $member->user_id = $user_id;
                     * $member->updated_by = $user ['id'];
                     */
                    // echo $member_id;

                    $criteria = array(
                        'user' => $member_id,
                        'role' => $role_id
                    );

                    $isMember = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclUserRole')->findBy($criteria);
                    // var_dump($isMember);
                    if (count($isMember) == 0) {
                        $member = new NmtApplicationAclUserRole();
                        $role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $role_id);
                        $member->setRole($role);
                        $m = $this->doctrineEM->find('Application\Entity\MlaUsers', $member_id);
                        $member->setUser($m);
                        $member->setUpdatedBy($u);
                        $member->setUpdatedOn(new \DateTime());
                        $this->doctrineEM->persist($member);
                        $this->doctrineEM->flush();
                    }
                }

                $redirectUrl = $request->getPost('redirectUrl');
                $this->redirect()->toUrl($redirectUrl);
            }
        }

        $id = (int) $this->params()->fromQuery('id');
        // $role = $this->aclRoleTable->getRole ( $id );
        $role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $id);

        /**
         *
         * @todo
         */
        $users = $this->aclRoleTable->getNoneMembersOfRole($id);

        return new ViewModel(array(
            'role' => $role,
            'users' => $users,
            'redirectUrl' => $redirectUrl
        ));
    }

    /**
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function grantAccessAction()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->getRequest()
            ->getHeader('Referer')
            ->getUri();

        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'email' => $this->identity()
        ));

        if ($request->isPost()) {

            $role_id = (int) $request->getPost('role_id');
            $resources = $request->getPost('resources');

            if (count($resources) > 0) {

                foreach ($resources as $r) {

                    // if not granted
                    $criteria = array(
                        'role' => $role_id,
                        'resource' => $r
                    );

                    $isGranted = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationAclRoleResource')->findBy($criteria);

                    if (count($isGranted) == 0) {
                        $e = new NmtApplicationAclRoleResource();

                        $role = $this->doctrineEM->find('Application\Entity\NmtApplicationAclRole', $role_id);
                        $e->setRole($role);

                        $resources = $this->doctrineEM->find('Application\Entity\NmtApplicationAclResource', $r);
                        $e->setResource($resources);

                        $e->setUpdatedBy($u);

                        $e->setUpdatedOn(new \DateTime());
                        $this->doctrineEM->persist($e);
                        $this->doctrineEM->flush();
                    }
                }

                $this->redirect()->toUrl("/application/role/grant-access?id=" . $role_id);
            }
        }

        // NO POST
        // =================================

        if (is_null($this->params()->fromQuery('perPage'))) {
            $resultsPerPage = 50;
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
        $role_id = $this->params()->fromQuery('id');

        /**
         *
         * @todo
         */
        $resources = $this->aclRoleTable->getNoneResourcesOfRole($role_id, 0, 0);

        $totalResults = count($resources);

        $paginator = null;
        if ($totalResults > $resultsPerPage) {
            $paginator = new Paginator($totalResults, $page, $resultsPerPage);
            $resources = $this->aclRoleTable->getNoneResourcesOfRole($role_id, ($paginator->maxInPage - $paginator->minInPage) + 1, $paginator->minInPage - 1);
        }

        return new ViewModel(array(
            'total_resources' => $totalResults,
            'role_id' => $role_id,
            'resources' => $resources,
            'paginator' => $paginator
        ));
    }

    public function getAclRoleTable()
    {
        return $this->aclRoleTable;
    }

    public function setAclRoleTable(AclRoleTable $aclRoleTable)
    {
        $this->aclRoleTable = $aclRoleTable;
        return $this;
    }

    public function getAclService()
    {
        return $this->aclService;
    }

    public function setAclService(AclService $aclService)
    {
        $this->aclService = $aclService;
        return $this;
    }

    // JS TREE
    protected function tree($data, $root)
    {
        $tree = $data->categories[$root];
        $children = $tree['children'];

        // inorder travesal

        if (count($children) > 0) {
            $this->tree = $this->tree . '<li id="' . $root . '" data-jstree=\'{ "opened" : true}\'> ' . ucwords($root) . '(' . count($children) . ")\n";
            $this->tree = $this->tree . '<ul>' . "\n";
            foreach ($children as $c) {
                if (count($c['children']) > 0) {
                    $this->tree($data, $c['instance']);
                } else {
                    $this->tree = $this->tree . '<li id="' . $c['instance'] . '" data-jstree=\'{}\'>' . $c['instance'] . ' </li>' . "\n";
                    $this->tree($data, $c['instance']);
                }
            }
            $this->tree = $this->tree . '</ul>' . "\n";

            $this->tree = $this->tree . '</li>' . "\n";
        }
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
