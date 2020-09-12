<?php
namespace Application\BaseEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 *         ORM@MappedSuperclass
 */
class BaseNmtProcurePr
{

    /**
     * One product has many features.
     * This is the inverse side.
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\NmtApplicationAttachment", mappedBy="pr")
     */
    protected $attachmentList;

    // ================================
    public function __construct()
    {
        $this->attachmentList = new ArrayCollection();
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAttachmentList()
    {
        return $this->attachmentList;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $attachmentList
     */
    public function setAttachmentList($attachmentList)
    {
        $this->attachmentList = $attachmentList;
    }
}
