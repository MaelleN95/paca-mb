<?php

namespace App\Controller;

use App\Entity\ToolType;
use App\Repository\ToolTypeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tool/type', name: 'tooltype_')]
final class ToolTypeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ToolTypeRepository $toolTypeRepository): Response
    {
        $toolsTypes = $toolTypeRepository->findBy([], ['name' => 'ASC']);

        return $this->render('tool_type/index.html.twig', [
            'toolsTypes' => $toolsTypes,
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(ToolType $toolType): Response
    {
        $tools = $toolType->getTools();

        return $this->render('tool_type/show.html.twig', [
            'toolType' => $toolType,
            'tools' => $tools,
        ]);
    }
}
