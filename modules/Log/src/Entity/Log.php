<?php
namespace Log\Entity;

use DateTime;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Job;
use Omeka\Entity\User;

/**
 * @Entity
 * @Table(
 *     indexes={
 *         @Index(name="owner_idx", columns={"owner_id"}),
 *         @Index(name="job_idx", columns={"job_id"}),
 *         @Index(name="reference_idx", columns={"reference"}),
 *         @Index(name="severity_idx", columns={"severity"})
 *     }
 * )
 * @HasLifecycleCallbacks
 */
class Log extends AbstractEntity
{
    /**
     * @var int
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\User"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="SET NULL"
     * )
     */
    protected $owner;

    /**
     * @ManyToOne(
     *     targetEntity="Omeka\Entity\Job"
     * )
     * @JoinColumn(
     *     nullable=true,
     *     onDelete="CASCADE"
     * )
     */
    protected $job;

    /**
     * @var string
     * @Column(
     *     length=190,
     *     options={"default"=""}
     * )
     */
    protected $reference = '';

    /**
     * @var int
     * @Column(
     *     type="integer",
     *     options={"default"=0}
     * )
     */
    protected $severity = 0;

    /**
     * @var string
     * @Column(type="text")
     */
    protected $message;

    /**
     * @var array
     * @Column(type="json_array")
     */
    protected $context;

    /**
     * @var DateTime
     * @Column(type="datetime")
     */
    protected $created;

    public function getId()
    {
        return $this->id;
    }

    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setJob(Job $job = null)
    {
        $this->job = $job;
    }

    public function getJob()
    {
        return $this->job;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    public function getSeverity()
    {
        return $this->severity;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setContext(array $context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setCreated(DateTime $created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @PrePersist
     */
    public function prePersist(LifecycleEventArgs $eventContext)
    {
        $this->created = new DateTime('now');
    }
}
