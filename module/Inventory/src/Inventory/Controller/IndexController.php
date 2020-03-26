<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Inventory\Controller;

use Zend\Session\Container;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
