<?php
namespace Application\Domain\Shared\Specification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractSpecification implements SpecificationInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\SpecificationInterface::isSatisfiedBy()
     */
    abstract public function isSatisfiedBy($subject);

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\SpecificationInterface::andSpec()
     */
    public function andSpec(SpecificationInterface $spec)
    {
        return new AndSpecification($this, $spec);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\SpecificationInterface::notSpec()
     */
    public function notSpec(SpecificationInterface $spec)
    {
        return new AndSpecification($spec);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Specification\SpecificationInterface::orSpec()
     */
    public function orSpec(SpecificationInterface $spec)
    {
        return new OrSpecification($this, $spec);
    }
}
