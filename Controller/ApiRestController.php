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
use Symfony\Component\HttpFoundation\Response;


class ApiRestController extends Controller
{
    public function postcategorie($categorie)
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
            $categorie_new = new Categories();
            $categorie_new->setName($categorie);
            $em->persist($categorie_new);
            $em->flush();
            $response = new Response();
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

    }
}