<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/category')]
final class CategoryController extends AbstractController
{
    #[Route('/{slug}', name: 'category_show')]
    public function show(string $slug, CategoryRepository $categoryRepo, ProductRepository $productRepo): Response
    {
        $category = $categoryRepo->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        $products = $productRepo->findBy(['category' => $category], ['title' => 'ASC']);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
