<?php

namespace PM\Service;
use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 *
 * @author nmt
 *        
 */
class PmNavigationFactory extends AbstractNavigationFactory
{
	/**
	 * Returns config name of the navigation
	 *
	 * @return string
	 */
	public function getName()
	{
		return "pm_navi";
	}
}