<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Link;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LinkController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
        // replace this example code with whatever you need
        return $this->render('layout.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/link/add", name="addLink")
     */
    public function addAction(Request $request) {
        // create a task and give it some dummy data for this example
        $link = new Link();

        $form = $this->createFormBuilder($link)
                ->add('url', TextType::class)
                ->add('description', TextType::class)
                ->add('tags', EntityType::class, array(
                    'class' => 'AppBundle:Tag',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                ))
                ->add('save', SubmitType::class, array('label' => 'Validation'))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($link);
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Link ajouté avec succès!')
            ;

            return $this->redirect($this->generateUrl('addLink', array('etat' => 'succes')
            ));
        }

        return $this->render('link/addLink.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/link/read", name="readLink")
     * @Template("link/readLink.html.twig")
     */
    public function readAction() {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Link');

        $links = $repository->findAll();

        return $this->render('link/readLink.html.twig', array(
                    'listeLinks' => $links
        ));
    }

    /**
     * @Route("/link/{id}/delete", name="deleteLink")
     */
    public function deleteAction($id, Request $request) {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Link');
        $link = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($link);
        $em->flush();

        // Suggestion: add a message in the flashbag
        // Redirect to the table page
        return $this->redirect($this->generateUrl('readLink'));
    }

    /**
     * @Route("/link/{id}/update", name="updateLink")
     */
    public function editAction($id, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $link = $em->getRepository('AppBundle:Link')->findOneById($id);
        if (!$link) {
            throw $this->createNotFoundException(
                    'No Link found for ID ' . $id
            );
        }


        $form = $this->createFormBuilder($link)
                ->add('url', TextType::class)
                ->add('description', TextType::class)
                ->add('tags', EntityType::class, array(
                    'class' => 'AppBundle:Tag',
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                ))
                ->add('save', SubmitType::class, array('label' => 'Modification'))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Link modifié avec succès!')
            ;

            return $this->redirect($this->generateUrl('readLink')
            );
        }

        return $this->render('link/updateLink.html.twig', array(
                    'form' => $form->createView()
        ));
    }

}
