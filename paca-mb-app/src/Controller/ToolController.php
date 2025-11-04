<?php

namespace App\Controller;

use App\Entity\Tool;
use App\Repository\ToolRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tool', name: 'tool_')]
final class ToolController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ToolRepository $toolRepository): Response
    {
        $tools = $toolRepository->findBy([], ['name' => 'ASC']);

        return $this->render('tool/index.html.twig', [
            'tools' => $tools,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(Tool $tool): Response
    {
        $products = $tool->getProducts();

        return $this->render('tool/show.html.twig', [
            'tool' => $tool,
            'products' => $products,
        ]);
    }
}
