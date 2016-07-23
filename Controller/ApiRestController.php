<?php
/**
 * Created by PhpStorm.
 * User: Quentin
 * Date: 16/07/2016
 * Time: 00:12
 */
namespace MonApiBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use MonApiBundle\Entity\Categories;
use MonApiBundle\Entity\Villes;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ApiRestController extends Controller
{

    /**
     * @Route("/api/add/categorie/")
     * @Method("POST")
     */
    public function postcategorieAction($categorie)
    {
        if(is_null($categorie))
        {
            $response = new Response();
            $response->setStatusCode(400);
            return $response;
        }
        else
        {
            $em = $this->getDoctrine()->getManager();
            $verif = $em->getRepository('MonApiBundle:Categories')->findOneBy(array('name' => $categorie));
            if(!$verif)
            {
                $categorie_new = new Categories();
                $categorie_new->setName($categorie);
                $em->persist($categorie_new);
                $em->flush();
                return new JsonResponse([
                    'success' => true,
                    'code'    => "200",
                    'message' => "Categorie ($categorie) ajoutée !",
                ]);
            }
            else
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Une categorie avec ce nom existe déjà",
                ]);
            }
        }
    }
    /**
     * @Route("/api/add/ville/")
     * @Method("POST")
     */
    public function postvilleAction($ville)
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($ville))
        {
            $response = new Response();
            $response->setStatusCode(400);
            return $response;
        }
        else
        {
            $existe = $em->getRepository('MonApiBundle:VillesFrance')->findOneBy(array('villeCodePostal' => $ville));
            if($existe)
            {
                $verif = $em->getRepository('MonApiBundle:Villes')->findOneBy(array('codePostal' => $ville));
                if(!$verif)
                {
                    $ville_new = new Villes();
                    $ville_new->setCodePostal($ville);
                    $em->persist($ville_new);
                    $em->flush();
                    return new JsonResponse([
                        'success' => true,
                        'code' => "200",
                        'message' => "Ville ($ville) ajoutée !",
                    ]);
                }
                else
                {
                    return new JsonResponse([
                        'success' => false,
                        'code'    => "409",
                        'message' => "Une ville avec ce code postal existe déjà",
                    ]);
                }
            }
            else
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Cette ville n'existe pas",
                ]);
            }
        }
    }
}