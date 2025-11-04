<?php
// src/Controller/Admin/DashboardController.php

namespace App\Controller\Admin;

use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Entity\News;
use App\Entity\Tool;
use App\Entity\ToolType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')] // protège tout le dashboard : accessible uniquement aux admins
class DashboardController extends AbstractDashboardController
{
    // Symfony injecte automatiquement ces services
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private EntityManagerInterface $em
    ) {}

    /**
     * La route principale du back-office (/admin)
     */
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Ici, tu as deux possibilités :
        // A - Rediriger directement vers un CRUD (par ex. la liste des produits)
        $url = $this->adminUrlGenerator
            ->setController(ProductCrudController::class)
            ->generateUrl();
        return $this->redirect($url);

        // B - Ou bien afficher un tableau de bord personnalisé avec des statistiques :
        // return $this->render('admin/dashboard.html.twig', [
        //     'stats' => $this->getStats(),
        // ]);
    }

    /**
     * Personnalise le titre et quelques options globales du back-office.
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration du site Paca Machine à Bois')
            ->renderContentMaximized() // Le contenu s’affiche sur toute la largeur
            ->setFaviconPath('favicon.ico');
    }

    /**
     * Déclare le menu latéral gauche : liens vers les entités ou routes.
     */
    public function configureMenuItems(): iterable
    {
        // SECTION = titre visuel du menu
        yield MenuItem::section('Tableau de bord');
        yield MenuItem::linkToDashboard('Accueil Admin', 'fa fa-home');

        yield MenuItem::section('Catalogue');
        yield MenuItem::linkToCrud('Produits', 'fa fa-box', Product::class);
        yield MenuItem::linkToCrud('Outils', 'fa fa-box', Tool::class);
        yield MenuItem::linkToCrud('Types d\'outils', 'fa fa-box', ToolType::class);
        yield MenuItem::linkToCrud('Fabricants', 'fa fa-box', Manufacturer::class);

        yield MenuItem::section('Contenu');
        yield MenuItem::linkToCrud('Actualités', 'fa fa-newspaper', News::class);

        yield MenuItem::section('Navigation');
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-globe', 'home');
    }

    /**
     * Exemple d’une méthode interne qui pourrait retourner des statistiques
     */
    private function getStats(): array
    {
        $products = $this->em->getRepository(Product::class)->count([]);
        $news = $this->em->getRepository(News::class)->count([]);

        return [
            'products' => $products,
            'news' => $news,
        ];
    }
}
