<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Calendar\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Calendar\Service\CalendarService;


/*
 * Control Panel Controller
 */
class IndexController extends AbstractActionController {
	
	protected $doctrineEM;
	protected $calendarService;
	
	
	/*
	 * Defaul Action
	 */
	public function indexAction()
	{
	    $calendar= $this->calendarService->drawMonthCal(null,null,'http://localhost:81/calendar');
	    return new ViewModel ( array (
	        'calendar' => $calendar,	        
	    ) );
	
	}
	
	
	/**
	 * 
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getDoctrineEM() {
		return $this->doctrineEM;
	}
	
	/**
	 * 
	 * @param EntityManager $doctrineEM
	 * @return \PM\Controller\IndexController
	 */
	public function setDoctrineEM(EntityManager $doctrineEM) {
		$this->doctrineEM = $doctrineEM;
		return $this;
	}
    /**
     * @return mixed
     */
    public function getCalendarService()
    {
        return $this->calendarService;
    }

    /**
     * @param mixed $calendarService
     */
    public function setCalendarService(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

	
	
}
