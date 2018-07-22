<?php

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Cache\Storage\StorageInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\Session\Container;
/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
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
     *  @return \Zend\Http\Response
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
     * 
     *  @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

   /**
    * 
    *  @param EntityManager $doctrineEM
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
