<?php
namespace Inventory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 *
 * @author Nguyen Mau Tri - Ngmautri@gmail.com
 *        
 */
class IndexController extends AbstractActionController
{

    public $userTable;

    public $authService;

    public $massage = 'NULL';

    public $articlePictureTable;

    /*
     * Defaul Action
     */
    public function indexAction()
    {
        /*
         * $session = new Container('MLA_USER');
         * $hasUser=$session->offsetExists('user');
         * if($hasUser)
         * {
         * $user=$session->offsetGet('user');
         *
         * //get user role
         * $user_id = $user['id'];
         * $aclUserRole= $this->getArticlePictureTable();
         * $roles = $aclUserRole;
         * }
         * $this->
         * return new ViewModel ( array (
         * 'user' => $aclUserRole,
         * ) );
         */
        return new ViewModel(array(
            'message' => $this->flashMessenger()->getCurrentInfoMessages()
        ));
    }

    private function getArticlePictureTable()
    {
        if (! $this->articlePictureTable) {
            $sm = $this->getServiceLocator();
            $this->articlePictureTable = $sm->get('User\Service\Acl');
        }
        return $this->articlePictureTable;
    }
}
