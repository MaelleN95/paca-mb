<?php

namespace App\Controller;

use App\Repository\ToolRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ToolRepository $toolRepo): Response
    {
        $tools = $toolRepo->findBy([], ['toolType' => 'ASC', 'name' => 'ASC']);

        return $this->render('main/index.html.twig', [
            'tools' => $tools,
        ]);
    }
}
