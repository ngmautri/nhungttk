<?php

namespace Production\Service;
use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ProductionNavigationFactory extends AbstractNavigationFactory
{
	/**
	 * Returns config name of the navigation
	 *
	 * @return string
	 */
	public function getName()
	{
		return "production_navi";
	}
}