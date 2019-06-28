<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Model\User;
use Doctrine\ORM\EntityManager;
use MLA\Files;
use Zend\Validator\EmailAddress;
use Zend\Session\Container;
use User\Model\UserTable;

class AuthController extends AbstractActionController
{

    public $userTable;

    public $authService;

    public $registerService;

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     *
     * @return \Zend\Http\Response|\Zend\View\Model\ViewModel
     */
    public function authenticateAction()
    {
        $request = $this->getRequest();
        $redirect = $this->params()->fromQuery('redirect');
        
        if ($redirect == null) {
            $redirect = '/inventory/item/list2';
        }
        $this->layout("User/login");
        
        // User is authenticated
        if ($this->getAuthService()->hasIdentity()) {
            // return $this->redirect ()->toRoute ( 'Inventory' );
            return $this->redirect()->toUrl($redirect);
        }
        
        if ($request->isPost()) {
            
            $email = $request->getPost('email');
            $password = $request->getPost('password');
            $redirect = $request->getPost('redirect');
            
            $errors = array();
            
            $validator = new EmailAddress();
            if (! $validator->isValid($email)) {
                $errors[] = 'Email addresse is not correct!';
            }
            
            if (count($errors) > 0) {
                
                // trigger log. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('authenticate.log', __CLASS__, array(
                    'priority' => 7,
                    'message' => '[' . $email . '] tried to login, but failed!'
                ));
                
                return new ViewModel(array(
                    'messages' => $errors,
                    'redirect' => $redirect
                ));
            }
            
            $this->authService->getAdapter()
                ->setIdentity($email)
                ->setCredential($password);
            $result = $this->authService->authenticate();
            
            if ($result->isValid()) {
                
                $user = $this->userTable->getUserByEmail($email);
                
                // create new session
                $session = new Container('MLA_USER');
                $session->offsetSet('user', $user);
                
                // trigger uploadPicture. AbtractController is EventManagerAware.
                $this->getEventManager()->trigger('authenticate.log', __CLASS__, array(
                    'priority' => 7,
                    'message' => '[' . $email . '] logged in successful!'
                ));
                
                $this->flashmessenger()->addMessage("Logged in successfully!");
                return $this->redirect()->toUrl($redirect);
            } else {
                
                return new ViewModel(array(
                    'messages' => $result->getMessages(),
                    'redirect' => $redirect
                ));
            }
        }
        
        // No POST
        
        return new ViewModel(array(
            'messages' => null,
            'redirect' => $redirect
        ));
    }

    /**
     */
    public function logoutAction()
    {
        $session = new Container('MLA_USER');
        $user = $session->offsetGet('user');
        $email = $user['email'];
        
        /* @var \Doctrine\ORM\EntityManager $doctrineEM ; */
        //$doctrineEM = $this->NmtPlugin()->doctrineEM();
        
        $session->getManager()->destroy();
        $this->authService->clearIdentity();
        
        // trigger uploadPicture. AbtractController is EventManagerAware.
        $this->getEventManager()->trigger('authenticate.log', __METHOD__, array(
            'priority' => 7,
            'message' => '[' . $email . '] logged out. Good bye!'
        ));
        
        /** @var \Doctrine\ORM\EntityManager $doctrineEM ; */
        // $doctrineEM = $this->NmtPlugin()->doctrineEM();
        
        /** @var \Application\Entity\MlaUsers $u ; */
        // $u = $doctrineEM->getRepository('\Application\Entity\MlaUsers')->findOneBy(array('email'=>$email));
        // echo $u->getEmail();
        
        //$this->flashmessenger()->addMessage("Good Bye!");
        return $this->redirect()->toUrl('/user/auth/authenticate');
    }

    // GETTER AND SETTER
    public function getUserTable()
    {
        return $this->userTable;
    }

    public function setUserTable(UserTable $userTable)
    {
        $this->userTable = $userTable;
        return $this;
    }

    public function getAuthService()
    {
        return $this->authService;
    }

    public function setAuthService($authService)
    {
        $this->authService = $authService;
        return $this;
    }

    public function getRegisterService()
    {
        return $this->registerService;
    }

    public function setRegisterService($registerService)
    {
        $this->registerService = $registerService;
        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * @param mixed $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

	
}
