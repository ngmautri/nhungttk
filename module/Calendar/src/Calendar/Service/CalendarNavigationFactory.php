<?php

namespace Calendar\Service;
use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 *
 * @author nmt
 *        
 */
class CalendarNavigationFactory extends AbstractNavigationFactory
{
	/**
	 * Returns config name of the navigation
	 *
	 * @return string
	 */
	public function getName()
	{
		return "calendar_navi";
	}
}