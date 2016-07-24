<?php

namespace MonApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Villes
 *
 * @ORM\Table(name="villes")
 * @ORM\Entity(repositoryClass="MonApiBundle\Repository\VillesRepository")
 */
class Villes
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
     * @var int
     * @Assert\Length(min = 5,
     *                minMessage = "Le code postal doit comporter au minimum {{ limit }} chiffres")
     * @ORM\Column(name="code_postal", type="integer")
     */
    private $codePostal;


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
     * Set codePostal
     *
     * @param integer $codePostal
     *
     * @return Villes
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return int
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }
}

