<?php
namespace App\Controller\Admin;

use App\Entity\Tool;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ToolCrudController extends AbstractCrudController
{

    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Tool::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Outil')
            ->setEntityLabelInPlural('Outils')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom');
        yield AssociationField::new('toolType', 'Type d’outil');
        yield FormField::addPanel()
            ->setLabel(
                '<a href="/admin?crudAction=new&crudControllerFqcn=App\Controller\Admin\ToolTypeCrudController"
                    target="_blank"
                    class="btn btn-secondary"
                    title="Si le type d\'outil n\'existe pas encore, cliquez ici pour en créer un nouveau"
                >
                    + Créer un type d\'outil
                </a>'
            );
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        try {
            parent::deleteEntity($entityManager, $entityInstance);
        } catch (ForeignKeyConstraintViolationException $e) {
            // Message visible dans EasyAdmin
            $this->addFlash('danger', sprintf(
                'Impossible de supprimer l’outil "%s" car il est encore lié à un ou plusieurs produits.',
                $entityInstance->getName()
            ));

            // Redirection propre vers la liste
            $url = $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction('index')
                ->generateUrl();

            // Redirection sans lever d’exception fatale
            header("Location: $url");
            exit;
        }
    }
}
