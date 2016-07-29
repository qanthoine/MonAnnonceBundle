<?php
/**
 * Created by PhpStorm.
 * User: Quentin
 * Date: 16/07/2016
 * Time: 00:12
 */
namespace MonApiBundle\Controller;


use MonApiBundle\Entity\Annonce;
use Proxies\__CG__\MonApiBundle\Entity\Images;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use MonApiBundle\Entity\Categories;
use MonApiBundle\Entity\Villes;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("/api/get/annonce/categorie/{categorie}/ville/{ville}/{page}", defaults={"page" = 1})
     * @Method("GET")
     */
    public function getcatevilleAction($categorie, $ville, $page = 1) // Utilise Ville Code Postal et Categorie SLUG et Page (page 1 par defaut)
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($categorie) || is_null($ville))
        {
            $response = new Response();
            $response->setStatusCode(400);
            return $response;
        }
        else
        {
            $recup_ville = $em->getRepository('MonApiBundle:Villes')->findOneBy(array('codePostal' => $ville));
            $recup_categorie = $em->getRepository('MonApiBundle:Categories')->findOneBy(array('slug' => $categorie));
            if(!$recup_ville || !$recup_categorie)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Cette categorie ou ville n'existe pas",
                ]);
            }
            $nb_annonce_page = 10;
            $count = $em->getRepository('MonApiBundle:Annonce')->countcategorieville($recup_ville, $recup_categorie);
            $nb_total_page = ceil($count/$nb_annonce_page);
            if($nb_total_page == 1)
            {
                $all_annonce = array();
                $annonce = $em->getRepository('MonApiBundle:Annonce')->findBy(array('villes' => $recup_ville, 'categories' => $recup_categorie) ,array('id' => 'asc'));
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
                    'message' => "Affichage de $count annonce(s) pour la ville : $ville et categorie $categorie",
                    'Annonces :' => $all_annonce,
                ]);
            }
            else
            {
                if($page > 0 & $page <= $nb_total_page)
                {
                    $first_page = (($page-1)*$nb_annonce_page);
                    $all_annonce = array();
                    $annonce = $em->getRepository('MonApiBundle:Annonce')->pagecategorieville($recup_ville, $recup_categorie, $nb_annonce_page, $first_page);
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
                        'message' => "Affichage de(s) annonce(s) pour la ville : $ville et categorie : $categorie",
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
     * @Route("/api/add/annonce")
     * @Method("POST")
     */
    public function postannonceAction(Request $request) // Requiert les paramètres obligatoire (Titre / Description / Categorie (ID) / Ville (ID))
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($request->query->get('titre')) || strlen($request->query->get('titre')) <= 5 || strlen($request->query->get('titre')) >= 60 || !is_string($request->query->get('titre')))
        {
            return new JsonResponse([
                'success' => false,
                'code'    => "409",
                'message' => "Erreur de paramètre",
                'info'    => "Erreur au niveau de Titre"
            ]);
        }
        else
        {
            $verif_titre = $em->getRepository('MonApiBundle:Annonce')->findOneBy(array('titre' => $request->query->get('titre')));
            if($verif_titre)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Le titre existe déjà",
                ]);
            }
        }
        if(is_null($request->query->get('description')) || strlen($request->query->get('description')) <= 10 || strlen($request->query->get('description')) >= 255 || !is_string($request->query->get('description')))
        {
            return new JsonResponse([
                'success' => false,
                'code'    => "409",
                'message' => "Erreur de paramètre",
                'info'    => "Erreur au niveau de Description"
            ]);
        }
        if(is_null($request->query->get('categorie')) || !is_numeric($request->query->get('categorie')))
        {
            return new JsonResponse([
                'success' => false,
                'code'    => "409",
                'message' => "Erreur de paramètre",
                'info'    => "Erreur au niveau de Categorie"
            ]);
        }
        else
        {
            $verif_categorie = $em->getRepository('MonApiBundle:Categories')->findOneBy(array('id' => $request->query->get('categorie')));
            if(!$verif_categorie)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "La categorie n'existe pas"
                ]);
            }
            else
            {
                $categorie = $verif_categorie;
            }
        }
        if(is_null($request->query->get('ville')) || !is_numeric($request->query->get('ville')))
        {
            return new JsonResponse([
                'success' => false,
                'code'    => "409",
                'message' => "Erreur de paramètre",
                'info'    => "Erreur au niveau de Ville"
            ]);
        }
        else
        {
            $verif_ville = $em->getRepository('MonApiBundle:Villes')->findOneBy(array('id' => $request->query->get('ville')));
            if(!$verif_ville)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "La ville n'existe pas"
                ]);
            }
            else
            {
                $ville = $verif_ville;
            }
        }
        if(is_null($request->query->get('prix')) || ($request->query->get('prix') >= 0 & is_numeric($request->query->get('prix'))))
        {
            $prix = $request->query->get('prix');
        }
        else
        {
            return new JsonResponse([
                'success' => false,
                'code'    => "409",
                'message' => "Erreur de paramètre",
                'info'    => "Erreur au niveau de Prix"
            ]);
        }
        $annonce = new Annonce();
        $annonce->setTitre($request->query->get('titre'));
        $annonce->setDescription($request->query->get('description'));
        $annonce->setCategories($categorie);
        $annonce->setVilles($ville);
        $annonce->setPrix($prix);
        $req_image = $request->files->all();
        foreach ($req_image as $images)
        {
            if (!$images->guessExtension() == ('jpg' || 'jpeg' || 'jpe' || 'bmp' || 'wbmp' || 'dib' || 'png')) {
                return new JsonResponse([
                    'success' => false,
                    'code' => "409",
                    'message' => "Erreur de paramètre",
                    'info' => "Mauvais format de l'image"
                ]);
            }
            $image = new Images();
            $image->setImage(uniqid() . '.' . $images->guessExtension());
            $annonce->addImage($image);
            $images->move('../web/uploads/', $image->getImage());
            $image->setAnnonce($annonce);
            $em->persist($image);
        }
        $em->persist($annonce);
        $em->flush();
        return new JsonResponse([
            'success' => true,
            'code' => "200",
            'message' => "Annonce ajoutée avec succès",
            'info' => 'Annonce n°' . $annonce->getId()
        ]);
    }

    /**
     * @Route("/api/edit/annonce/{id]")
     * @Method("POST")
     */
    public function postannonceeditAction($id, Request $request) // Utilise Annonce ID
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($id))
        {
            $response = new Response();
            $response->setStatusCode(400);
            return $response;
        }
        $annonce = $em->getRepository('MonApiBundle:Annonce')->findOneBy(array('id' => $id));
        if(!$annonce)
        {
            return new JsonResponse([
                'success' => false,
                'code'    => "409",
                'message' => "L'annonce n'existe pas",
            ]);
        }
        if(!is_null($request->query->get('titre')))
        {
            if(strlen($request->query->get('titre')) >= 5 & strlen($request->query->get('titre')) <= 60 & is_string($request->query->get('titre')))
            {
                $verif_titre = $em->getRepository('MonApiBundle:Annonce')->findOneBy(array('titre' => $request->query->get('titre')));
                if($verif_titre)
                {
                    return new JsonResponse([
                        'success' => false,
                        'code'    => "409",
                        'message' => "Le titre existe déjà",
                    ]);
                }
                else
                {
                    $titre = $request->query->get('titre');
                }
            }
            else
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Erreur de paramètre",
                    'info'    => "Erreur au niveau de Titre"
                ]);
            }
        }
        else
        {
            $titre = $annonce->gettitre();
        }
        if(!is_null($request->query->get('description')))
        {
            if(strlen($request->query->get('description')) >= 10 & strlen($request->query->get('description')) <= 255 & is_string($request->query->get('description')))
            {
                $description = $request->query->get('description');
            }
            else
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Erreur de paramètre",
                    'info'    => "Erreur au niveau de Description"
                ]);
            }
        }
        else
        {
            $description = $annonce->getdescription();
        }
        if(!is_null($request->query->get('categorie')) & is_numeric($request->query->get('categorie')))
        {
            $verif_categorie = $em->getRepository('MonApiBundle:Categories')->findOneBy(array('id' => $request->query->get('categorie')));
            if(!$verif_categorie)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "La categorie n'existe pas",
                ]);
            }
            $categorie = $verif_categorie;
        }
        else
        {
            $categorie = $annonce->getCategories();
        }
        if(!is_null($request->query->get('ville')) & is_numeric($request->query->get('ville')))
        {
            $verif_ville = $em->getRepository('MonApiBundle:Villes')->findOneBy(array('id' => $request->query->get('ville')));
            if(!$verif_ville)
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "La ville n'existe pas",
                ]);
            }
            $ville = $verif_ville;
        }
        else
        {
            $ville = $annonce->getVilles();
        }
        if(!is_null($request->query->get('prix')))
        {
            if($request->query->get('prix') >= 0 & is_numeric($request->query->get('prix')))
            {
                $prix = $request->query->get('prix');
            }
            else
            {
                return new JsonResponse([
                    'success' => false,
                    'code'    => "409",
                    'message' => "Erreur de paramètre",
                    'info'    => "Erreur au niveau de Prix"
                ]);
            }
        }
        else
        {
            $prix = $annonce->getprix();
        }
        $annonce->setTitre($titre);
        $annonce->setDescription($description);
        $annonce->setCategories($categorie);
        $annonce->setVilles($ville);
        $annonce->setPrix($prix);
        $em->persist($annonce);
        $em->flush();
        return new JsonResponse([
            'success' => true,
            'code' => "200",
            'message' => "Annonce modifiée avec succès",
            'info' => 'Annonce n°'.$annonce->getId()
        ]);
    }
}