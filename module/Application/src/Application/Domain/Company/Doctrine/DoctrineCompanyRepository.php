<?php
namespace Application\Domain\Company\Doctrine;

use Application\Domain\Company\CompanyRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Application\Domain\Company\Company;
use Application\Domain\Shared\Currency;
use Application\Domain\Company\CompanyId;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineCompanyRepository implements CompanyRepositoryInterface
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
        $this->em = $em;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\CompanyRepositoryInterface::findAll()
     */
    public function findAll()
    {
        $companyList = array();
        $list = $this->em->getRepository("\Application\Entity\NmtApplicationCompany")->findAll();
        if (count($list) > 0) {

            foreach ($list as $l) {
                /** @var  \Application\Entity\NmtApplicationCompany $l*/

                $currency = $l->getDefaultCurrency()->getCurrency();

                $company = new Company(new CompanyId($l->getId()), $l->getCompanyName(), new Currency($currency));
                $companyList[] = $company;
            }
        }

        return $companyList;
    }
}
