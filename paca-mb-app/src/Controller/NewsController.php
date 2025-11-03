<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/news')]
final class NewsController extends AbstractController
{
    #[Route('/', name: 'news_index')]
    public function index(NewsRepository $newsRepo): Response
    {
        $news = $newsRepo->findBy(['isActive' => true], ['publishedAt' => 'DESC']);

        return $this->render('news/index.html.twig', [
            'newsList' => $news,
        ]);
    }

    #[Route('/{slug}', name: 'news_show')]
    public function show(string $slug, NewsRepository $newsRepo): Response
    {
        $item = $newsRepo->findOneBy(['slug' => $slug, 'isActive' => true]);
        if (!$item) {
            throw $this->createNotFoundException('Actualité introuvable.');
        }

        return $this->render('news/show.html.twig', [
            'news' => $item,
        ]);
    }
}
