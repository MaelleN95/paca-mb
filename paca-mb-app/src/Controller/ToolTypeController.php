<?php

namespace App\Controller;

use App\Entity\ToolType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/tool/type', name: 'tooltype_')]
final class ToolTypeController extends AbstractController
{
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
