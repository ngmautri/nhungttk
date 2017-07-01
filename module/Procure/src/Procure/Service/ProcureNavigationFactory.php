<?php

namespace Procure\Service;
use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 *
 * @author nmt
 *        
 */
class ProcureNavigationFactory extends AbstractNavigationFactory
{
	/**
	 * Returns config name of the navigation
	 *
	 * @return string
	 */
	public function getName()
	{
		return "procure_navi";
	}
}