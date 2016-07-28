<?php

namespace MonApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Annonce
 *
 * @ORM\Table(name="annonce")
 * @ORM\Entity(repositoryClass="MonApiBundle\Repository\AnnonceRepository")
 * @UniqueEntity(
 *     fields={"titre"},
 *     errorPath="titre",
 *     message="Le titre est déjà utilisé. Merci d'en choisir un autre"
 * )
 */
class Annonce
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     * @Assert\Length(min = 5,
     *                max = 60,
     *                minMessage = "Le titre doit comporter au minimum {{ limit }} caractères",
     *                maxMessage = "Le titre doit comporter au maximum {{ limit}} caractères")
     * @Assert\NotBlank(message = "Le Champs de doit pas être vide")
     * @ORM\Column(name="titre", type="string", length=60, unique=true)
     */
    private $titre;

    /**
     * @Gedmo\Slug(fields={"titre"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     * @Assert\Length(min = 10,
     *                max = 255,
     *                minMessage = "La description doit comporter au minimum {{ limit }} caractères",
     *                maxMessage = "La description doit comporter au maximum {{ limit}} caractères")
     * @Assert\NotBlank(message = "Le Champs de doit pas être vide")
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float", nullable=true)
     */
    private $prix;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="MonApiBundle\Entity\Categories", cascade={"persist"})
     */
    private $categories;
    /**
     * @ORM\OneToMany(targetEntity="Images", mappedBy="annonce", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="MonApiBundle\Entity\Villes", cascade={"persist"})
     */
    private $villes;

    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->date = new \DateTime();
    }


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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Annonce
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Annonce
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Annonce
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Annonce
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Annonce
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set categories
     *
     * @param string $categories
     *
     * @return Annonce
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return string
     */
    public function getCategories()
    {
        return $this->categories;
    }
    
    /**
     * Add image
     *
     * @param \MonApiBundle\Entity\Images $image
     *
     * @return Annonce
     */
    public function addImage(\MonApiBundle\Entity\Images $image)
    {
        $this->images[] = $image;
        return $this;
    }
    /**
     * Remove image
     *
     * @param \MonApiBundle\Entity\Images $image
     */
    public function removeImage(\MonApiBundle\Entity\Images $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set villes
     *
     * @param string $villes
     *
     * @return Annonce
     */
    public function setVilles($villes)
    {
        $this->villes = $villes;

        return $this;
    }

    /**
     * Get villes
     *
     * @return string
     */
    public function getVilles()
    {
        return $this->villes;
    }
}

