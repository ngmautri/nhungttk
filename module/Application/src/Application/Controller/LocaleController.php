<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\I18n\Translator;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;
/**
 *
 * @author nmt
 *        
 */
class LocaleController extends AbstractActionController {
    
      
    protected $doctrineEM;
    protected $translatorService;
    protected $cacheService;
    
    
    
		
	/*
	 * Defaul Action
	 */
	public function indexAction() {
	}

	
    /**
     * 
     */
	public function changeLocaleAction() {
	    
	    $request = $this->getRequest();
	    
	    if ($request->getHeader('Referer') == null) {
	        return $this->redirect()->toRoute('access_denied');
	    }
	    
	    $redirectUrl = $this->getRequest()
	    ->getHeader('Referer')
	    ->getUri();
	
	    // New Container will get he Language Session if the SessionManager already knows the language session.
	    $session = new Container('locale');
	    $config = $this->serviceLocator->get('config');
	    $locale = $this->params ()->fromQuery ( 'locale' );
	    
	    if (isset($config['locale']['available'][$locale]))
	    {
	        $session->locale = $locale;
	        $this->translatorService->setLocale($locale);
	        $this->flashMessenger()->addMessage('Locale changed to:  "' . $locale . '"');
	        
	    }
	    
	    return $this->redirect()->toUrl($redirectUrl);
	    
	}
	
    /**
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     * @param field_type $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
    /**
     * @return mixed
     */
    public function getTranslatorService()
    {
        return $this->translatorService;
    }

    /**
     * @param mixed $translatorService
     */
    public function setTranslatorService(Translator $translatorService)
    {
        $this->translatorService = $translatorService;
    }
    /**
     * @return mixed
     */
    public function getCacheService()
    {
        return $this->cacheService;
    }

    /**
     * @param mixed $cacheService
     */
    public function setCacheService(StorageInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }



	

	
}
