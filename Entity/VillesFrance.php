<?php

namespace MonApiBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * VillesFrance
 *
 * @ORM\Table(name="villes_france")
 * @ORM\Entity
 */
class VillesFrance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ville_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $villeId;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_nom", type="string", length=45)
     */
    private $villeNom;

    /**
     * @var string
     *
     * @ORM\Column(name="ville_code_postal", type="string", length=255)
     */
    private $villeCodePostal;


    /**
     * Get villeId
     *
     * @return int
     */
    public function getVilleId()
    {
        return $this->villeId;
    }

    /**
     * Get villeNom
     *
     * @return string
     */
    public function getVilleNom()
    {
        return $this->villeNom;
    }

    /**
     * Set villeNom
     *
     * @param string $villesNom
     *
     * @return VillesFrance
     */
    public function setVilleNom($villeNom)
    {
        $this->VilleNom = $villeNom;

        return $this;
    }

    /**
     * Get villeCodePostal
     *
     * @return string
     */
    public function getVilleCodePostal()
    {
        return $this->villeCodePostal;
    }

    /**
     * Set villeCodePostal
     *
     * @param int $villeCodePostal
     *
     * @return VillesFrance
     */
    public function setVilleCodePostal($villeCodePostal)
    {
        $this->villeCodePostal = $villeCodePostal;

        return $this;
    }

}

