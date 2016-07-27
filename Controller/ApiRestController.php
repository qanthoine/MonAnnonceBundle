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
    /**
     * @Route("/api/get/annonce/")
     * @Method("GET")
     */
    public function getannonceAction($annonce)
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($annonce))
        {
            $response = new Response();
            $response->setStatusCode(400);
            return $response;
        }
        else
        {
            $recuperation = $em->getRepository('MonApiBundle:Annonce')->findOneBy(array('id' => $annonce));
            $images = $em->getRepository('MonApiBundle:Images')->findBy(array('annonce' => $recuperation));
            if(!$images)
            {
                $image_recup = "Aucune Image";
            }
            else
            {
                $image_recup = array();
                foreach ($images as $image)
                {
                    array_push($image_recup, 'http://127.0.0.1/api/web/app_dev.php/show/image/'.$image->getimage().'');
                }
            }
            if($recuperation->getprix() == null)
            {
                $prix = 0;
            }
            else
            {
                $prix = $recuperation->getprix();
            }
            if(!$recuperation)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Cette annonce n'existe pas",
                ]);
            }
            return new JsonResponse([
                'success' => true,
                'code' => "200",
                'message' => "Annonce récupérée avec succès",
                'id' => $recuperation->getid(),
                'titre' => $recuperation->gettitre(),
                'description' => $recuperation->getdescription(),
                'prix' => $prix.'€',
                'categorie' => $recuperation->getcategories()->getname(),
                'ville' => $recuperation->getvilles()->getcodePostal(),
                'images' => $image_recup
            ]);
        }
    } // Utilise Annonce ID
    /**
     * @Route("/api/delete/annonce/")
     * @Method("DELETE")
     */
    public function deleteannonceAction($annonce)
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($annonce))
        {
            $response = new Response();
            $response->setStatusCode(400);
            return $response;
        }
        else
        {
            $recuperation = $em->getRepository('MonApiBundle:Annonce')->findOneBy(array('id' => $annonce));
            if(!$recuperation)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Cette annonce n'existe pas",
                ]);
            }
            $em->remove($recuperation);
            $em->flush();
            return new JsonResponse([
                'success' => true,
                'code'    => "200",
                'message' => "Annonce ($annonce) suprimée !",
            ]);
        }
    } // Utilise Annonce ID

    /**
     * @Route("/api/get/annonce/categorie/{categorie}/{page}", defaults={"page" = 1})
     * @Method("GET")
     */
    public function getannoncecategorieAction($categorie, $page = 1) // Utilise Categorie SLUG et Page (page 1 par defaut)
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($categorie))
        {
            $response = new Response();
            $response->setStatusCode(400);
            return $response;
        }
        else
        {
            $recup_categorie = $em->getRepository('MonApiBundle:Categories')->findOneBy(array('slug' => $categorie));
            if(!$recup_categorie)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Cette categorie n'existe pas",
                ]);
            }
            $nb_annonce_page = 10;
            $count = $em->getRepository('MonApiBundle:Annonce')->countannonce($recup_categorie);
            $nb_total_page = ceil($count/$nb_annonce_page);
            if($nb_total_page == 1)
            {
                $all_annonce = array();
                $annonce = $em->getRepository('MonApiBundle:Annonce')->findBy(array('categories' => $recup_categorie),array('id' => 'asc'));
                foreach ($annonce as $annonceNew)
                {
                    if($annonceNew->getprix() == null)
                    {
                        $prix = 0;
                    }
                    else
                    {
                        $prix = $annonceNew->getprix();
                    }
                    $images = $em->getRepository('MonApiBundle:Images')->findBy(array('annonce' => $annonceNew->getid()));
                    if(!$images)
                    {
                        $image_recup = "Aucune Image";
                    }
                    else
                    {
                        $image_recup = array();
                        foreach ($images as $image)
                        {
                            array_push($image_recup, 'http://127.0.0.1/api/web/app_dev.php/show/image/'.$image->getimage().'');
                        }
                    }
                    $transforme_annonce = array('Annonce n°' => $annonceNew->getid(), 'Titre' => $annonceNew->gettitre(), 'Description' => $annonceNew->getdescription(), 'Prix' => $prix.'€', 'Categorie' => $recup_categorie->getname(), 'Ville' => $annonceNew->getVilles()->getcodePostal(), 'Image' => $image_recup);
                    array_push($all_annonce, $transforme_annonce);
                }
                return new JsonResponse([
                    'success' => true,
                    'code' => "200",
                    'message' => "Affichage de $count annonce(s) pour la categorie : $categorie",
                    'Annonces :' => $all_annonce,
                ]);
            }
            else
            {
                if($page > 0 & $page <= $nb_total_page)
                {
                    $first_page = (($page-1)*$nb_annonce_page);
                    $all_annonce = array();
                    $annonce = $em->getRepository('MonApiBundle:Annonce')->annoncepage($recup_categorie, $nb_annonce_page, $first_page);
                    foreach ($annonce as $annonceNew)
                    {
                        if($annonceNew->getprix() == null)
                        {
                            $prix = 0;
                        }
                        else
                        {
                            $prix = $annonceNew->getprix();
                        }
                        $images = $em->getRepository('MonApiBundle:Images')->findBy(array('annonce' => $annonceNew->getid()));
                        if(!$images)
                        {
                            $image_recup = "Aucune Image";
                        }
                        else
                        {
                            $image_recup = array();
                            foreach ($images as $image)
                            {
                                array_push($image_recup, 'http://127.0.0.1/api/web/app_dev.php/show/image/'.$image->getimage().'');
                            }
                        }
                        $transforme_annonce = array('Annonce n°' => $annonceNew->getid(), 'Titre' => $annonceNew->gettitre(), 'Description' => $annonceNew->getdescription(), 'Prix' => $prix.'€', 'Categorie' => $recup_categorie->getname(), 'Ville' => $annonceNew->getVilles()->getcodePostal(), 'Image' => $image_recup);
                        array_push($all_annonce, $transforme_annonce);
                    }
                    return new JsonResponse([
                        'success' => true,
                        'code' => "200",
                        'message' => "Affichage de(s) annonce(s) pour la categorie : $categorie",
                        'Annonces :' => $all_annonce,
                        'info' => "Page $page sur $nb_total_page affichées ($count annonces en tout)"
                    ]);
                }
                else
                {
                    return new JsonResponse([
                        'success' => false,
                        'code'    => "409",
                        'message' => "Verifier les parametres",
                        'info'    => "Erreur de la page"
                    ]);
                }
            }
        }
    }

    /**
     * @Route("/api/get/annonce/ville/{ville}/{page}", defaults={"page" = 1})
     * @Method("GET")
     */
    public function getannoncevilleAction($ville, $page = 1) // Utilise Ville Code Postal et Page (page 1 par defaut)
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
            $recup_ville = $em->getRepository('MonApiBundle:Villes')->findOneBy(array('codePostal' => $ville));
            if(!$recup_ville)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Cette categorie n'existe pas",
                ]);
            }
            $nb_annonce_page = 10;
            $count = $em->getRepository('MonApiBundle:Annonce')->countannonceville($recup_ville);
            $nb_total_page = ceil($count/$nb_annonce_page);
            if($nb_total_page == 1)
            {
                $all_annonce = array();
                $annonce = $em->getRepository('MonApiBundle:Annonce')->findBy(array('villes' => $recup_ville),array('id' => 'asc'));
                foreach ($annonce as $annonceNew)
                {
                    if($annonceNew->getprix() == null)
                    {
                        $prix = 0;
                    }
                    else
                    {
                        $prix = $annonceNew->getprix();
                    }
                    $images = $em->getRepository('MonApiBundle:Images')->findBy(array('annonce' => $annonceNew->getid()));
                    if(!$images)
                    {
                        $image_recup = "Aucune Image";
                    }
                    else
                    {
                        $image_recup = array();
                        foreach ($images as $image)
                        {
                            array_push($image_recup, 'http://127.0.0.1/api/web/app_dev.php/show/image/'.$image->getimage().'');
                        }
                    }
                    $transforme_annonce = array('Annonce n°' => $annonceNew->getid(), 'Titre' => $annonceNew->gettitre(), 'Description' => $annonceNew->getdescription(), 'Prix' => $prix.'€', 'Categorie' => $annonceNew->getCategories()->getname(), 'Ville' => $annonceNew->getVilles()->getcodePostal(), 'Image' => $image_recup);
                    array_push($all_annonce, $transforme_annonce);
                }
                return new JsonResponse([
                    'success' => true,
                    'code' => "200",
                    'message' => "Affichage de $count annonce(s) pour la ville : $ville",
                    'Annonces :' => $all_annonce,
                ]);
            }
            else
            {
                if($page > 0 & $page <= $nb_total_page)
                {
                    $first_page = (($page-1)*$nb_annonce_page);
                    $all_annonce = array();
                    $annonce = $em->getRepository('MonApiBundle:Annonce')->annoncepageville($recup_ville, $nb_annonce_page, $first_page);
                    foreach ($annonce as $annonceNew)
                    {
                        if($annonceNew->getprix() == null)
                        {
                            $prix = 0;
                        }
                        else
                        {
                            $prix = $annonceNew->getprix();
                        }
                        $images = $em->getRepository('MonApiBundle:Images')->findBy(array('annonce' => $annonceNew->getid()));
                        if(!$images)
                        {
                            $image_recup = "Aucune Image";
                        }
                        else
                        {
                            $image_recup = array();
                            foreach ($images as $image)
                            {
                                array_push($image_recup, 'http://127.0.0.1/api/web/app_dev.php/show/image/'.$image->getimage().'');
                            }
                        }
                        $transforme_annonce = array('Annonce n°' => $annonceNew->getid(), 'Titre' => $annonceNew->gettitre(), 'Description' => $annonceNew->getdescription(), 'Prix' => $prix.'€', 'Categorie' => $annonceNew->getCategories()->getname(), 'Ville' => $annonceNew->getVilles()->getcodePostal(), 'Image' => $image_recup);
                        array_push($all_annonce, $transforme_annonce);
                    }
                    return new JsonResponse([
                        'success' => true,
                        'code' => "200",
                        'message' => "Affichage de(s) annonce(s) pour la ville : $ville",
                        'Annonces :' => $all_annonce,
                        'info' => "Page $page sur $nb_total_page affichées ($count annonces en tout)"
                    ]);
                }
                else
                {
                    return new JsonResponse([
                        'success' => false,
                        'code'    => "409",
                        'message' => "Verifier les parametres",
                        'info'    => "Erreur de la page"
                    ]);
                }
            }
        }
    }
}