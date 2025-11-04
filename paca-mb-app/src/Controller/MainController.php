<?php

namespace App\Controller;

use App\Repository\ToolTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ToolTypeRepository $toolTypeRepository): Response
    {
        $toolTypes = $toolTypeRepository->findBy([], ['name' => 'ASC']);

        return $this->render('main/index.html.twig', [
            'toolTypes' => $toolTypes,
        ]);
    }
}
