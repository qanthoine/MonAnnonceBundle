<?php
/**
 * Created by PhpStorm.
 * User: Quentin
 * Date: 11/07/2016
 * Time: 04:35
 */
namespace MonApiBundle\Controller;

use MonApiBundle\Entity\Annonce;
use MonApiBundle\Entity\Categories;
use MonApiBundle\Form\AnnonceType;
use MonApiBundle\Form\CategoriesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('MonApiBundle:Annonce')->findAnnonce();
        $categorie = new Categories();
        $form = $this->createForm(CategoriesType::class, $categorie);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('mon_api_homepage');
        }
        return $this->render('MonApiBundle:Api:index.html.twig', array('annonce' => $annonce, 'form' => $form->createView()));
    }
    public function addAction(Request $request, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        if (is_null($slug)) {
            $annonce = new Annonce();
            $notice = "Annonce créee";
            $page_title ="Nouvelle Annonce";
            $form = $this->createForm(AnnonceType::class, $annonce);
            $form->handleRequest($request);
        }
        else{
            $annonce = $em->getRepository('MonApiBundle:Annonce')->findOneBy(array('slug' => $slug));
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
            $verif = $em->getRepository('MonApiBundle:VillesFrance')->findBy(array('villeCodePostal' => $annonce->getVilles()));
            if(!$verif)
            {
                $request->getSession()->getFlashBag()->add('ville', "La ville n'existe pas !");
                return $this->redirect($this->generateUrl('mon_api_add'));
            }
            $em->persist($annonce);
            {
                foreach ($annonce->getImages() as $image)
                {
                    $image->setAnnonce($annonce);
                }
            }
            $em->flush();
            $request->getSession()->getFlashBag()->add('message', $notice);
            return $this->redirectToRoute('mon_api_homepage');
        }
        return $this->render('MonApiBundle:Api:add.html.twig', array('form' => $form->createView(), 'titre' => $page_title, 'info' => $annonce));
    }
    public function showAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('MonApiBundle:Annonce')->findOneby(array('slug' => $slug));
        if(!$annonce)
        {
            $request->getSession()->getFlashBag()->add('message', "L'annonce n'existe pas !");
            return $this->redirect($this->generateUrl('mon_api_homepage'));
        }
        return $this->render('MonApiBundle:Api:show.html.twig', array('annonce' => $annonce));
    }
    public function showimageAction(Request $request, $image)
    {
        $em = $this->getDoctrine()->getManager();
        $verif = $em->getRepository('MonApiBundle:Images')->findOneby(array('image' => $image));
        if(!$verif)
        {
            $request->getSession()->getFlashBag()->add('message', "L'image n'est pas visualisable !");
            return $this->redirect($this->generateUrl('mon_api_homepage'));
        }
        return $this->render('MonApiBundle:Api:showimage.html.twig', array('image' => $verif));
    }
    public function deleteAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('MonApiBundle:Annonce')->findOneby(array('slug' => $slug));
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
}