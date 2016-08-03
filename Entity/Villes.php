<?php

namespace MonAnnonceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Villes
 *
 * @ORM\Table(name="villes")
 * @ORM\Entity(repositoryClass="MonAnnonceBundle\Repository\VillesRepository")
 * @UniqueEntity(
 *     fields={"codePostal"},
 *     errorPath="codePostal",
 *     message="Le code postal est déjà utilisé. Merci d'en choisir un autre"
 * )
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
     * @Assert\NotBlank(message = "Le Champs de doit pas être vide")
     * @ORM\Column(name="code_postal", type="integer", unique=true)
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

