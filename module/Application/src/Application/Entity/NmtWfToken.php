<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfToken
 *
 * @ORM\Table(name="nmt_wf_token", indexes={@ORM\Index(name="nmt_wf_token_idx", columns={"case_id"}), @ORM\Index(name="nmt_wf_token_FK2_idx", columns={"workflow_id"}), @ORM\Index(name="nmt_wf_token_FK3_idx", columns={"place_id"}), @ORM\Index(name="nmt_wf_token_FK4_idx", columns={"token_enabled_by"}), @ORM\Index(name="nmt_wf_token_FK5_idx", columns={"token_cancelled_by"}), @ORM\Index(name="nmt_wf_token_FK6_idx", columns={"token_consumed_by"}), @ORM\Index(name="nmt_wf_token_FK6_idx1", columns={"note_id"}), @ORM\Index(name="nmt_wf_token_IDX1", columns={"subject_id"}), @ORM\Index(name="nmt_wf_token_IDX2", columns={"subject_token"}), @ORM\Index(name="nmt_wf_token_IDX3", columns={"subject_class"})})
 * @ORM\Entity
 */
class NmtWfToken
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
     * @var string
     *
     * @ORM\Column(name="place_name", type="string", length=45, nullable=true)
     */
    private $placeName;

    /**
     * @var string
     *
     * @ORM\Column(name="token_status", type="string", length=45, nullable=true)
     */
    private $tokenStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enabled_date", type="datetime", nullable=true)
     */
    private $enabledDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cancelled_date", type="datetime", nullable=true)
     */
    private $cancelledDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="consumed_date", type="datetime", nullable=true)
     */
    private $consumedDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="subject_id", type="integer", nullable=true)
     */
    private $subjectId;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_token", type="string", length=45, nullable=true)
     */
    private $subjectToken;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_class", type="string", length=100, nullable=true)
     */
    private $subjectClass;

    /**
     * @var \Application\Entity\NmtWfCase
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfCase")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="case_id", referencedColumnName="case_id")
     * })
     */
    private $case;

    /**
     * @var \Application\Entity\NmtWfWorkflow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfWorkflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="workflow_id", referencedColumnName="id")
     * })
     */
    private $workflow;

    /**
     * @var \Application\Entity\NmtWfPlace
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfPlace")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="place_id", referencedColumnName="id")
     * })
     */
    private $place;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="token_enabled_by", referencedColumnName="id")
     * })
     */
    private $tokenEnabledBy;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="token_cancelled_by", referencedColumnName="id")
     * })
     */
    private $tokenCancelledBy;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="token_consumed_by", referencedColumnName="id")
     * })
     */
    private $tokenConsumedBy;

    /**
     * @var \Application\Entity\NmtWfNode
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfNode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="note_id", referencedColumnName="node_id")
     * })
     */
    private $note;



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
     * Set placeName
     *
     * @param string $placeName
     *
     * @return NmtWfToken
     */
    public function setPlaceName($placeName)
    {
        $this->placeName = $placeName;

        return $this;
    }

    /**
     * Get placeName
     *
     * @return string
     */
    public function getPlaceName()
    {
        return $this->placeName;
    }

    /**
     * Set tokenStatus
     *
     * @param string $tokenStatus
     *
     * @return NmtWfToken
     */
    public function setTokenStatus($tokenStatus)
    {
        $this->tokenStatus = $tokenStatus;

        return $this;
    }

    /**
     * Get tokenStatus
     *
     * @return string
     */
    public function getTokenStatus()
    {
        return $this->tokenStatus;
    }

    /**
     * Set enabledDate
     *
     * @param \DateTime $enabledDate
     *
     * @return NmtWfToken
     */
    public function setEnabledDate($enabledDate)
    {
        $this->enabledDate = $enabledDate;

        return $this;
    }

    /**
     * Get enabledDate
     *
     * @return \DateTime
     */
    public function getEnabledDate()
    {
        return $this->enabledDate;
    }

    /**
     * Set cancelledDate
     *
     * @param \DateTime $cancelledDate
     *
     * @return NmtWfToken
     */
    public function setCancelledDate($cancelledDate)
    {
        $this->cancelledDate = $cancelledDate;

        return $this;
    }

    /**
     * Get cancelledDate
     *
     * @return \DateTime
     */
    public function getCancelledDate()
    {
        return $this->cancelledDate;
    }

    /**
     * Set consumedDate
     *
     * @param \DateTime $consumedDate
     *
     * @return NmtWfToken
     */
    public function setConsumedDate($consumedDate)
    {
        $this->consumedDate = $consumedDate;

        return $this;
    }

    /**
     * Get consumedDate
     *
     * @return \DateTime
     */
    public function getConsumedDate()
    {
        return $this->consumedDate;
    }

    /**
     * Set subjectId
     *
     * @param integer $subjectId
     *
     * @return NmtWfToken
     */
    public function setSubjectId($subjectId)
    {
        $this->subjectId = $subjectId;

        return $this;
    }

    /**
     * Get subjectId
     *
     * @return integer
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * Set subjectToken
     *
     * @param string $subjectToken
     *
     * @return NmtWfToken
     */
    public function setSubjectToken($subjectToken)
    {
        $this->subjectToken = $subjectToken;

        return $this;
    }

    /**
     * Get subjectToken
     *
     * @return string
     */
    public function getSubjectToken()
    {
        return $this->subjectToken;
    }

    /**
     * Set subjectClass
     *
     * @param string $subjectClass
     *
     * @return NmtWfToken
     */
    public function setSubjectClass($subjectClass)
    {
        $this->subjectClass = $subjectClass;

        return $this;
    }

    /**
     * Get subjectClass
     *
     * @return string
     */
    public function getSubjectClass()
    {
        return $this->subjectClass;
    }

    /**
     * Set case
     *
     * @param \Application\Entity\NmtWfCase $case
     *
     * @return NmtWfToken
     */
    public function setCase(\Application\Entity\NmtWfCase $case = null)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get case
     *
     * @return \Application\Entity\NmtWfCase
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set workflow
     *
     * @param \Application\Entity\NmtWfWorkflow $workflow
     *
     * @return NmtWfToken
     */
    public function setWorkflow(\Application\Entity\NmtWfWorkflow $workflow = null)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Get workflow
     *
     * @return \Application\Entity\NmtWfWorkflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }

    /**
     * Set place
     *
     * @param \Application\Entity\NmtWfPlace $place
     *
     * @return NmtWfToken
     */
    public function setPlace(\Application\Entity\NmtWfPlace $place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return \Application\Entity\NmtWfPlace
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set tokenEnabledBy
     *
     * @param \Application\Entity\MlaUsers $tokenEnabledBy
     *
     * @return NmtWfToken
     */
    public function setTokenEnabledBy(\Application\Entity\MlaUsers $tokenEnabledBy = null)
    {
        $this->tokenEnabledBy = $tokenEnabledBy;

        return $this;
    }

    /**
     * Get tokenEnabledBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getTokenEnabledBy()
    {
        return $this->tokenEnabledBy;
    }

    /**
     * Set tokenCancelledBy
     *
     * @param \Application\Entity\MlaUsers $tokenCancelledBy
     *
     * @return NmtWfToken
     */
    public function setTokenCancelledBy(\Application\Entity\MlaUsers $tokenCancelledBy = null)
    {
        $this->tokenCancelledBy = $tokenCancelledBy;

        return $this;
    }

    /**
     * Get tokenCancelledBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getTokenCancelledBy()
    {
        return $this->tokenCancelledBy;
    }

    /**
     * Set tokenConsumedBy
     *
     * @param \Application\Entity\MlaUsers $tokenConsumedBy
     *
     * @return NmtWfToken
     */
    public function setTokenConsumedBy(\Application\Entity\MlaUsers $tokenConsumedBy = null)
    {
        $this->tokenConsumedBy = $tokenConsumedBy;

        return $this;
    }

    /**
     * Get tokenConsumedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getTokenConsumedBy()
    {
        return $this->tokenConsumedBy;
    }

    /**
     * Set note
     *
     * @param \Application\Entity\NmtWfNode $note
     *
     * @return NmtWfToken
     */
    public function setNote(\Application\Entity\NmtWfNode $note = null)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return \Application\Entity\NmtWfNode
     */
    public function getNote()
    {
        return $this->note;
    }
}
