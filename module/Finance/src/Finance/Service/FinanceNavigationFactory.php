<?php

namespace Finance\Service;
use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 *
 * @author nmt
 *        
 */
class FinanceNavigationFactory extends AbstractNavigationFactory
{
	/**
	 * Returns config name of the navigation
	 *
	 * @return string
	 */
	public function getName()
	{
		return "finance_navi";
	}
}