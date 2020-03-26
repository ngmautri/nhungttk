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
class IndexController extends AbstractActionController
{

    protected $doctrineEM;

    protected $calendarService;

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        $calendar = $this->calendarService->createMonthView(null, null, 'http://localhost:81/calendar');
        return new ViewModel(array(
            'calendar' => $calendar
        ));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function monthAction()
    {
        $request = $this->getRequest();

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $mm = $this->params()->fromQuery('mm');
        $yy = $this->params()->fromQuery('yy');

        $monthName = array(
            "January",
            "February",
            "March",
            "April",
            "May",
            "Juni",
            "Juli",
            "August",
            "September",
            "October",
            "November",
            "December"
        );

        $current_month = date("n") - 1;
        $current_year = date("Y");

        if ($yy == 0) :
            $yy = $current_year;
	    endif;

        if ($mm == 0) :
            $mm = $current_month;
	    endif;

        if ($mm < 0) {
            $mm = 11;
            $yy -= 1;
        }

        if ($mm > 11) {
            $mm = 0;
            $yy += 1;
        }

        $isCurrentMonth = 0;

        if (($mm == $current_month) and ($yy == $current_year)) {
            $isCurrentMonth = 1;
        }

        $calendar = $this->calendarService->createMonthView($mm, $yy, 'http://localhost:81/calendar');
        return new ViewModel(array(
            'calendar' => $calendar,
            'mm' => $mm,
            'mm_name' => $monthName[$mm],
            'yy' => $yy,
            'isCurrentMonth' => $isCurrentMonth
        ));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function month1Action()
    {
        $request = $this->getRequest();

        // accepted only ajax request
        if (! $request->isXmlHttpRequest()) {
            return $this->redirect()->toRoute('access_denied');
        }

        $this->layout("layout/user/ajax");

        if ($request->getHeader('Referer') == null) {
            return $this->redirect()->toRoute('access_denied');
        }

        $mm = $this->params()->fromQuery('mm');
        $yy = $this->params()->fromQuery('yy');

        $monthName = array(
            "January",
            "February",
            "March",
            "April",
            "May",
            "Juni",
            "Juli",
            "August",
            "September",
            "October",
            "November",
            "December"
        );

        $current_month = date("n") - 1;
        $current_year = date("Y");

        if ($yy == 0) :
            $yy = $current_year;
	    endif;

        if ($mm == 0) :
            $mm = $current_month;
	    endif;

        if ($mm < 0) {
            $mm = 11;
            $yy -= 1;
        }

        if ($mm > 11) {
            $mm = 0;
            $yy += 1;
        }

        $isCurrentMonth = 0;

        if (($mm == $current_month) and ($yy == $current_year)) {
            $isCurrentMonth = 1;
        }

        $calendar = $this->calendarService->createMonthView($mm, $yy, 'http://localhost:81/calendar', 1);
        return new ViewModel(array(
            'calendar' => $calendar,
            'mm' => $mm,
            'mm_name' => $monthName[$mm],
            'yy' => $yy,
            'isCurrentMonth' => $isCurrentMonth
        ));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function weekAction()
    {
        $mm = $this->params()->fromQuery('mm');
        $yy = $this->params()->fromQuery('yy');

        if ($yy == 0) :
            $yy = date('Y');
	    endif;

        $calendar = $this->calendarService->createMonthView(null, null, 'http://localhost:81/calendar');
        return new ViewModel(array(
            'calendar' => $calendar,
            'mm' => $mm,
            'yy' => $mm
        ));
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param EntityManager $doctrineEM
     * @return \PM\Controller\IndexController
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getCalendarService()
    {
        return $this->calendarService;
    }

    /**
     *
     * @param mixed $calendarService
     */
    public function setCalendarService(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }
}
