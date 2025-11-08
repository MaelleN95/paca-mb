<?php
namespace App\Controller\Admin;

use App\Entity\Manufacturer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ManufacturerCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Manufacturer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Fabricant')
            ->setEntityLabelInPlural('Fabricants')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom');

        yield UrlField::new('webSite', 'Site web')->hideOnIndex();

        yield ImageField::new('logo', 'Logo')
            ->setBasePath('/uploads/manufacturers')
            ->onlyOnIndex();

        yield TextField::new('logoFile', 'Logo')
            ->setFormType(VichImageType::class)
            ->onlyOnForms();
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        try {
            parent::deleteEntity($entityManager, $entityInstance);
        } catch (ForeignKeyConstraintViolationException $e) {
            // Message affiché dans l’interface EasyAdmin
            $this->addFlash('danger', sprintf(
                'Impossible de supprimer le fabricant "%s" car il est encore lié à un ou plusieurs produits.',
                $entityInstance->getName()
            ));

            // Redirection propre vers la liste des fabricants
            $url = $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction('index')
                ->generateUrl();

            // Redirection immédiate sans lever d’erreur
            header('Location: ' . $url);
            exit;
        }
    }
}
