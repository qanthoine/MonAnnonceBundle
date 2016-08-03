<?php
/**
 * Created by PhpStorm.
 * User: Quentin
 * Date: 11/07/2016
 * Time: 04:35
 */
namespace MonAnnonceBundle\Controller;

use MonAnnonceBundle\Entity\Annonce;
use MonAnnonceBundle\Entity\Categories;
use MonAnnonceBundle\Entity\Villes;
use MonAnnonceBundle\Form\AnnonceType;
use MonAnnonceBundle\Form\CategoriesType;
use MonAnnonceBundle\Form\VillesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SiteController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('MonAnnonceBundle:Annonce')->findAnnonce();
        $categorie = new Categories();
        $form_categorie = $this->createForm(CategoriesType::class, $categorie);
        $form_categorie->handleRequest($request);
        $ville = new Villes();
        $form_ville = $this->createForm(VillesType::class, $ville);
        $form_ville->handleRequest($request);
        if($form_categorie->isValid())
        {
            $em->persist($categorie);
            $em->flush();
            $request->getSession()->getFlashBag()->add('message', "Categorie ajoutée !");
            return $this->redirectToRoute('mon_api_homepage');
        }
        if($form_ville->isValid())
        {
            $verif = $em->getRepository('MonAnnonceBundle:VillesFrance')->findBy(array('villeCodePostal' => $ville->getCodePostal()));
            if(!$verif)
            {
                $request->getSession()->getFlashBag()->add('message', "La ville n'existe pas !");
                return $this->redirect($this->generateUrl('mon_api_homepage'));
            }
            $em->persist($ville);
            $em->flush();
            $request->getSession()->getFlashBag()->add('message', "Ville ajoutée !");
            return $this->redirectToRoute('mon_api_homepage');
        }
        return $this->render('MonAnnonceBundle:Api:index.html.twig', array('annonce' => $annonce, 'form_categorie' => $form_categorie->createView(), 'form_ville' => $form_ville->createView()));
    }
    public function addAction(Request $request, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        $verif_categorie = $em->getRepository('MonAnnonceBundle:Categories')->findAll();
        $verif_ville = $em->getRepository('MonAnnonceBundle:Villes')->findAll();
        if(!$verif_categorie)
        {
            $request->getSession()->getFlashBag()->add('message', "Merci de créer une categorie !");
            return $this->redirect($this->generateUrl('mon_api_homepage'));
        }
        if(!$verif_ville)
        {
            $request->getSession()->getFlashBag()->add('message', "Merci de créer une ville !");
            return $this->redirect($this->generateUrl('mon_api_homepage'));
        }
        if (is_null($slug)) {
            $annonce = new Annonce();
            $notice = "Annonce créee";
            $page_title ="Nouvelle Annonce";
            $form = $this->createForm(AnnonceType::class, $annonce);
            $form->handleRequest($request);
        }
        else
        {
            $annonce = $em->getRepository('MonAnnonceBundle:Annonce')->findOneBy(array('slug' => $slug));
            if(!$annonce)
            {
                $request->getSession()->getFlashBag()->add('message', "L'annonce n'existe pas !");
                return $this->redirect($this->generateUrl('mon_api_add'));
            }
            $notice = "Annonce modifiée";
            $page_title ="Edition de l'Annonce";
            $form = $this->createForm(AnnonceType::class, $annonce);
            $form->remove('images');
            $form->handleRequest($request);
        }
        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($annonce);
            foreach ($annonce->getImages() as $image)
            {
                $image->setAnnonce($annonce);
            }
            $em->flush();
            $request->getSession()->getFlashBag()->add('message', $notice);
            return $this->redirectToRoute('mon_api_homepage');
        }
        return $this->render('MonAnnonceBundle:Api:add.html.twig', array('form' => $form->createView(), 'titre' => $page_title, 'info' => $annonce));
    }
    public function showAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('MonAnnonceBundle:Annonce')->findOneby(array('slug' => $slug));
        if(!$annonce)
        {
            $request->getSession()->getFlashBag()->add('message', "L'annonce n'existe pas !");
            return $this->redirect($this->generateUrl('mon_api_homepage'));
        }
        return $this->render('MonAnnonceBundle:Api:show.html.twig', array('annonce' => $annonce));
    }
    public function showimageAction(Request $request, $image)
    {
        $em = $this->getDoctrine()->getManager();
        $verif = $em->getRepository('MonAnnonceBundle:Images')->findOneby(array('image' => $image));
        if(!$verif)
        {
            $request->getSession()->getFlashBag()->add('message', "L'image n'est pas visualisable !");
            return $this->redirect($this->generateUrl('mon_api_homepage'));
        }
        return $this->render('MonAnnonceBundle:Api:showimage.html.twig', array('image' => $verif));
    }
    public function deleteAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('MonAnnonceBundle:Annonce')->findOneby(array('slug' => $slug));
        if(!$annonce)
        {
            $request->getSession()->getFlashBag()->add('message', "L'annonce n'existe pas !");
            return $this->redirectToRoute('mon_api_homepage');
        }
        $em->remove($annonce);
        $em->flush();
        $request->getSession()->getFlashBag()->add('message', 'Annonce supprimée !');
        return $this->redirectToRoute('mon_api_homepage');
    }
    public function searchAction($categorie = null, $ville = null)
    {
        $em = $this->getDoctrine()->getManager();
        if(is_null($categorie)) // Categorie est null, j'affiche toutes les categories puis toutes les villes de chaque categorie
        {
            $categorie = $em->getRepository('MonAnnonceBundle:Categories')->findAll();
            $ville = $em->getRepository('MonAnnonceBundle:Annonce')->findVilles();
            return $this->render('MonAnnonceBundle:Api:search.html.twig', array('categorie' => $categorie, 'ville' => $ville));
        }
        else
        {
            if(is_null($ville))
            {
                $ville = $em->getRepository('MonAnnonceBundle:Annonce')->findOne();
                return $this->render('MonAnnonceBundle:Api:search.html.twig', array('categorie' => $categorie, 'ville' => $ville));
            }
            else
            {
                $categorie_entity = $em->getRepository('MonAnnonceBundle:Categories')->findOneby(array('slug' => $categorie));
                $ville_entity = $em->getRepository('MonAnnonceBundle:Villes')->findOneby(array('codePostal' => $ville));
                $annonce = $em->getRepository('MonAnnonceBundle:Annonce')->findCateVille($categorie_entity, $ville_entity);
                return $this->render('MonAnnonceBundle:Api:search.html.twig', array('annonce' => $annonce, 'categorie' => $categorie, 'ville' => $ville));
            }
        }
    }
}