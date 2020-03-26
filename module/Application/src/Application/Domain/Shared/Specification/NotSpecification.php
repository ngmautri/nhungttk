<?php
namespace Application\Domain\Shared\Specification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class NotSpecification extends AbstractSpecification
{

    /**
     *
     * @var SpecificationInterface
     */
    private $spec1;

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\AbstractSpecification::isSatisfiedBy()
     */
    public function isSatisfiedBy($subject)
    {
        return ! $this->spec1->isSatisfiedBy($subject);
    }

    /**
     *
     * @param SpecificationInterface $spec1
     */
    public function __construct(SpecificationInterface $spec1)
    {
        $this->spec1 = $spec1;
    }
}