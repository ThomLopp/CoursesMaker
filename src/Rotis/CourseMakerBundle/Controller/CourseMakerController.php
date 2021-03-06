<?php

namespace Rotis\CourseMakerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CourseMakerController extends Controller
{
    public function accueilAction()
    {
        return $this->render('RotisCourseMakerBundle:CourseMaker:accueil.html.twig');
    }

    public function contactAction()
    {
        return $this->render('RotisCourseMakerBundle:CourseMaker:contact.html.twig');
    }

    public function faqAction()
    {
        $edition = $this->getDoctrine()->getRepository('RotisCourseMakerBundle:Edition')->findLast();

        $tarifs = $this->getDoctrine()->getRepository('RotisCourseMakerBundle:Tarif')->findTarifsByEdition($edition->getId());
        return $this->render('RotisCourseMakerBundle:CourseMaker:faq.html.twig',array(
            'tarifs' => $tarifs,
        ));
    }

    public function parcoursAction()
    {
        return $this->render('RotisCourseMakerBundle:CourseMaker:parcours.html.twig');
    }
}
