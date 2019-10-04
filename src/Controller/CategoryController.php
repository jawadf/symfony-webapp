<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController 
{
    /**
     * @Route("/category", name="category")
     */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');


        $category = new Category();


        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('feed');
        }

        return $this->render('category/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
