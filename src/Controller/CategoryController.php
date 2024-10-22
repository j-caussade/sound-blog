<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'name' => 'Category',
            'categories' => $categories,
        ]);
    }

    #[Route('/category/new', name: 'app_category_new')]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()) {
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_category');
        }
        return $this->render('category/new.html.twig', [
            'name' => 'New category',
            'form' => $form->createView(),
        ]);
    }
}
