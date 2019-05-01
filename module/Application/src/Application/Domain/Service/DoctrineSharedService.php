<?php
namespace Application\Domain\Service;

use Doctrine\ORM\EntityManager;
use Application\Domain\Exception\InvalidArgumentException;
use Application\Domain\Shared\Currency;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineSharedService implements SharedServiceInterface
{

    /**
     *
     * @var EntityManager
     */
    private $em;

    /**
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        if ($em == null) {
            throw new InvalidArgumentException("Doctrine Entity manager not found!");
        }
        $this->em = $em;
    }

    public function getMeasurementUnitList()
    {}

    public function getCountryList()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Service\SharedServiceInterface::getCurrencyList()
     */
    public function getCurrencyList()
    {
        $currencyList = array();
        $list = $this->em->getRepository("\Application\Entity\NmtApplicationCurrency")->findAll();
        if (count($list) > 0) {

            foreach ($list as $l) {
                /** @var  \Application\Entity\NmtApplicationCurrency $l*/

                $currency = new Currency($l->getCurrency());
                $currencyList[] = $currency;
            }
        }

        return $currencyList;
    }
}
