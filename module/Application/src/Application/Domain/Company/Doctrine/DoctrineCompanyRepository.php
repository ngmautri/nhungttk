<?php
namespace Application\Domain\Company\Doctrine;

use Application\Domain\Company\CompanyRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Application\Domain\Company\Company;
use Application\Domain\Shared\Currency;
use Application\Domain\Shared\Department;
use Application\Domain\Company\CompanyId;
use Application\Domain\Exception\InvalidArgumentException;

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
        if ($em == null) {
            throw new InvalidArgumentException("Doctrine Entity manager not found!");
        }
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

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\CompanyRepositoryInterface::getById()
     */
    public function getById($id)
    {
        $criteria = array(
            "id" => $id
        );

        /**
         *
         * @var \Application\Entity\NmtApplicationCompany $entity ;
         */
        $entity = $this->em->getRepository("\Application\Entity\NmtApplicationCompany")->findOneBy($criteria);
        if ($entity == null) {
            return null;
        }

        /**
         *
         * @var \Application\Entity\NmtApplicationCurrency $currency ;
         */
        $currency = $entity->getDefaultCurrency();

        if ($currency == null) {
            throw new InvalidArgumentException("Curreny is not set");
        }

        $company = new Company(new CompanyId("uuid", $entity->getId()), $entity->getCompanyName(), new Currency($currency->getCurrency()));
        return $company;
    }
    
    
    /**
     * 
     */
    public function getByUUID($uuid)
    {}

    public function store(Company $company)
    {}
    public function addWarehouse(Company $company, $warehouse)
    {}

    public function addDeparment(Company $company, Department $department)
    {}


}
