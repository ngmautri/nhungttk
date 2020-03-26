<?php
namespace Application\Infrastructure\AggregateRepository;

use Application\Domain\Company\CompanyQueryRepositoryInterface;
use Application\Infrastructure\Mapper\CompanyMapper;
use Application\Domain\Company\GenericCompany;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineCompanyQueryRepository extends AbstractDoctrineRepository implements CompanyQueryRepositoryInterface
{

    public function getPostingPeriod($periodId)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\CompanyQueryRepositoryInterface::getById()
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
        $entity = $this->doctrineEM->getRepository("\Application\Entity\NmtApplicationCompany")->findOneBy($criteria);
        $snapshot = CompanyMapper::createDetailSnapshot($entity);

        $entityRoot = new GenericCompany();
        $entityRoot->makeFromDetailsSnapshot($snapshot);
        return $entityRoot;
    }

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}
}
