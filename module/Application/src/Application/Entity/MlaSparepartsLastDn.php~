<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaSparepartsLastDn
 *
 * @ORM\Table(name="mla_spareparts_last_dn", uniqueConstraints={@ORM\UniqueConstraint(name="sparepart_id_UNIQUE", columns={"sparepart_id"})}, indexes={@ORM\Index(name="mla_articles_last_dn_FK2_idx", columns={"last_workflow_id"})})
 * @ORM\Entity
 */
class MlaSparepartsLastDn
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Application\Entity\MlaSpareparts
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaSpareparts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sparepart_id", referencedColumnName="id")
     * })
     */
    private $sparepart;

    /**
     * @var \Application\Entity\MlaDeliveryItemsWorkflows
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaDeliveryItemsWorkflows")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_workflow_id", referencedColumnName="id")
     * })
     */
    private $lastWorkflow;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sparepart
     *
     * @param \Application\Entity\MlaSpareparts $sparepart
     *
     * @return MlaSparepartsLastDn
     */
    public function setSparepart(\Application\Entity\MlaSpareparts $sparepart = null)
    {
        $this->sparepart = $sparepart;

        return $this;
    }

    /**
     * Get sparepart
     *
     * @return \Application\Entity\MlaSpareparts
     */
    public function getSparepart()
    {
        return $this->sparepart;
    }

    /**
     * Set lastWorkflow
     *
     * @param \Application\Entity\MlaDeliveryItemsWorkflows $lastWorkflow
     *
     * @return MlaSparepartsLastDn
     */
    public function setLastWorkflow(\Application\Entity\MlaDeliveryItemsWorkflows $lastWorkflow = null)
    {
        $this->lastWorkflow = $lastWorkflow;

        return $this;
    }

    /**
     * Get lastWorkflow
     *
     * @return \Application\Entity\MlaDeliveryItemsWorkflows
     */
    public function getLastWorkflow()
    {
        return $this->lastWorkflow;
    }
}
