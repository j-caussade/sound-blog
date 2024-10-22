<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{
    #[Route('/tag', name: 'app_tag')]
    public function index(TagRepository $tagRepository): Response
    {
        $tags = $tagRepository->findAll();
        return $this->render('tag/index.html.twig', [
            'name' => 'Tag',
            'tags' => $tags
        ]);
    }

    #[Route('/tag/new', name: 'app_tag_new')]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()) {
            $entityManagerInterface->persist($tag);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_tag');
        }
        return $this->render('tag/new.html.twig', [
            'name' => 'New tag',
            'form' => $form->createView(),
        ]);
    }
}
