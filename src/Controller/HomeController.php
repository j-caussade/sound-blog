<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Tag;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    private string $uploadsDir;
    public function __construct(string $uploadsDir)
    {
        $this->uploadsDir = $uploadsDir;
    }

    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('home/index.html.twig', [
            'name' => 'Home',
            'articles' => $articles,
        ]);
    }

    #[Route('/article/write', name:'app_article_write')]
    // #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->uploadsDir,
                        $newFilename
                    );
                    $article->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Downloading doesn\'t work, please try again !');
                }
            } else {
                $article->setImage('white_label.webp');
            }

            $entityManagerInterface->persist($article);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_home');
        }
        return $this->render('home/write.html.twig', [
            'name' => 'Write',
            'form' => $form->createView(),
        ]);

    }

    #[Route('/article/{id}', name: 'app_article_display')]
    public function displayArticle(int $id, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->find($id);
        return $this->render('home/display.html.twig', ['article'=>$article]);
    }

    #[Route('/article/{id}/edit', name: 'app_article_display_edit')]
    public function editDisplayArticle(int $id, Request $request, ArticleRepository $articleRepository, EntityManagerInterface $entityManagerInterface, Article $article): Response
    {
        $oldImage = $article->getImage();

        $article = $articleRepository->find($id);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
    
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->uploadsDir,
                        $newFilename
                    );
                    $article->setImage($newFilename);

                    if ($oldImage && file_exists($this->uploadsDir . '/' . $oldImage)) {
                        unlink($this->uploadsDir . '/' . $oldImage);
                    }
                    
                } catch (FileException $e) {
                    $this->addFlash('error', 'Downloading doesn\'t work, please try again !');
                }
            }
    
            $entityManagerInterface->persist($article);
            $entityManagerInterface->flush();

            


            return $this->redirectToRoute('app_home');
        }
        return $this->render('home/edit.html.twig', [
            'article' => $article,
            'name' => 'Edit',
            'form' => $form->createView(),
        ]);
    }
}
