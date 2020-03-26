<?php
namespace Application\Domain\Shared\Specification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface SpecificationInterface
{

    /**
     *
     * @param object $subject
     */
    public function isSatisfiedBy($subject);

    /**
     *
     * @param SpecificationInterface $spec
     */
    public function andSpec(SpecificationInterface $spec);

    /**
     *
     * @param SpecificationInterface $spec
     */
    public function orSpec(SpecificationInterface $spec);

    /**
     *
     * @param SpecificationInterface $spec
     */
    public function notSpec(SpecificationInterface $spec);
}
