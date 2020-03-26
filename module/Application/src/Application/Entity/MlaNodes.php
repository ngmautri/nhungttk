<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaNodes
 *
 * @ORM\Table(name="mla_nodes")
 * @ORM\Entity
 */
class MlaNodes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="node", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $node;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=45, nullable=false)
     */
    private $label;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Application\Entity\MlaNodes", inversedBy="ancestor")
     * @ORM\JoinTable(name="mla_closure",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ancestor", referencedColumnName="node")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="descendant", referencedColumnName="node")
     *   }
     * )
     */
    private $descendant;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->descendant = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get node
     *
     * @return integer
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return MlaNodes
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Add descendant
     *
     * @param \Application\Entity\MlaNodes $descendant
     *
     * @return MlaNodes
     */
    public function addDescendant(\Application\Entity\MlaNodes $descendant)
    {
        $this->descendant[] = $descendant;

        return $this;
    }

    /**
     * Remove descendant
     *
     * @param \Application\Entity\MlaNodes $descendant
     */
    public function removeDescendant(\Application\Entity\MlaNodes $descendant)
    {
        $this->descendant->removeElement($descendant);
    }

    /**
     * Get descendant
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDescendant()
    {
        return $this->descendant;
    }
}
