<?php

namespace MonAnnonceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Images
 *
 * @ORM\Table(name="images")
 * @ORM\Entity(repositoryClass="MonAnnonceBundle\Repository\ImagesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Images
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Annonce", inversedBy="images")
     */
    private $annonce;

    private $file;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Images
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get file
     *
     * @return mixed
     */
    public function getFile(){
        return $this->file;
    }

    /**
     * Set File
     *
     * @param UploadedFile|null $file
     * @return $this
     */
    public function setFile(UploadedFile $file){
        $this->file = $file;
        return $this;
    }

    /**
     * Set advert
     *
     * @param \MonAnnonceBundle\Entity\Annonce $annonce
     *
     * @return Images
     */
    public function setAnnonce(\MonAnnonceBundle\Entity\Annonce $annonce = null)
    {
        $this->annonce = $annonce;
        return $this;
    }

    /**
     * Get annonce
     *
     * @return \MonAnnonceBundle\Entity\Annonce
     */
    public function getAnnonce()
    {
        return $this->annonce;
    }

    /**
     * @ORM\PrePersist()
     */
    public function preUpload(){
        if(is_null($this->file))
            return;
        $this->image = uniqid().'.'.$this->file->guessExtension();
    }
    /**
     * @ORM\PostPersist()
     */
    public function upload(){
        if(is_null($this->file))
            return;
        $this->file->move('../web/uploads/', $this->image);
    }
}
