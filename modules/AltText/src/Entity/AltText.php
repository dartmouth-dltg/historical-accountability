<?php
namespace AltText\Entity;

use Omeka\Entity\AbstractEntity;
use Omeka\Entity\Media;

/**
 * @Entity
 */
class AltText extends AbstractEntity
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @OneToOne(targetEntity="Omeka\Entity\Media")
     * @JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $media;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $altText;

    public function getId()
    {
        return $this->id;
    }

    public function setMedia(Media $media)
    {
        $this->media = $media;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function setAltText($altText)
    {
        $this->altText = $altText;
    }

    public function getAltText()
    {
        return $this->altText;
    }
}
