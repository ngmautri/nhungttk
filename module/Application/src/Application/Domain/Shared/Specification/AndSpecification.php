<?php
namespace Application\Domain\Shared\Specification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AndSpecification extends AbstractSpecification
{

    /**
     *
     * @var SpecificationInterface
     */
    private $spec1;

    /**
     *
     * @var SpecificationInterface
     */
    private $spec2;

    /**
     * 
     * {@inheritDoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        return $this->spec1->isSatisfiedBy($subject) && $this->spec2->isSatisfiedBy($subject);
    }

    /**
     *
     * @param SpecificationInterface $spec1
     * @param SpecificationInterface $spec2
     */
    public function __construct(SpecificationInterface $spec1, SpecificationInterface $spec2)
    {
        $this->spec1 = $spec1;
        $this->spec2 = $spec2;
    }
}