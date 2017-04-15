<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class TagController extends Controller {

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
     * @Route("/tag/add", name="addTag")
     */
    public function addAction(Request $request) {
        // create a task and give it some dummy data for this example
        $tag = new Tag();

        $form = $this->createFormBuilder($tag)
                ->add('name', TextType::class)
                ->add('save', SubmitType::class, array('label' => 'Validation'))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Tag ajouté avec succès!')
            ;

            return $this->redirect($this->generateUrl('addTag', array('etat' => 'succes')
            ));
        }

        return $this->render('tag/addTag.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/tag/read", name="readTag")
     * @Template("tag/readTag.html.twig")
     */
    public function readAction() {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $tags = $repository->findAll();

        return $this->render('tag/readTag.html.twig', array(
                    'listeTags' => $tags
        ));
    }

    /**
     * @Route("/tag/{id}/delete", name="deleteTag")
     */
    public function deleteAction($id, Request $request) {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Tag');
        $tag = $repository->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($tag);
        $em->flush();

        // Suggestion: add a message in the flashbag
        // Redirect to the table page
        return $this->redirect($this->generateUrl('readTag'));
    }

    /**
     * @Route("/tag/{name}/update", name="updateTag")
     */
    public function editAction($name, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('AppBundle:Tag')->findOneByName($name);
        if (!$tags) {
            throw $this->createNotFoundException(
                    'No Tag found for name ' . $name
            );
        }


        $form = $this->createFormBuilder($tags)
                ->add('name', TextType::class)
                ->add('save', SubmitType::class, array('label' => 'Modification'))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Tag modifié avec succès!')
            ;

            return $this->redirect($this->generateUrl('readTag')
            );
        }

        return $this->render('tag/updateTag.html.twig', array(
                    'form' => $form->createView()
        ));
    }

}
