<?php
namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductImageType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Produits')
            ->setEntityLabelInSingular('Produit')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des produits')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('isUsed')
            ->add('tool')
            ->add('manufacturer');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Nom du produit');

        yield TextField::new('reference', 'Référence')
            ->setFormTypeOption('disabled', true)
            ->hideOnForm()
            ->hideOnIndex();

        yield TextEditorField::new('description', 'Description')
            ->hideOnIndex();

        yield AssociationField::new('tool', 'Type d’outil');

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

        yield AssociationField::new('manufacturer', 'Fabricant');

        yield FormField::addPanel()
            ->setLabel(
                '<a href="/admin?crudAction=new&crudControllerFqcn=App\Controller\Admin\ManufacturerCrudController"
                    target="_blank"
                    class="btn btn-secondary"
                    title="Si le fabricant n\'existe pas encore, cliquez ici pour en créer un nouveau"
                >
                    + Créer un fabricant
                </a>'
            );

        yield MoneyField::new('price', 'Prix')
            ->setCurrency('EUR')
            ->setNumDecimals(2);

        yield BooleanField::new('isUsed', 'Produit d’occasion ?');

        yield CollectionField::new('productImages', 'Images')
            ->setEntryType(ProductImageType::class)
            ->setEntryIsComplex(true)
            ->setFormTypeOptions(['by_reference' => false])
            ->onlyOnForms();

        yield ImageField::new('firstImage', 'Image')
            ->setBasePath('/uploads/product_images/')
            ->onlyOnIndex();

        yield ArrayField::new('technicalSpecifications', 'Caractéristiques techniques')
            ->hideOnIndex();

        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm();

        yield DateTimeField::new('updatedAt', 'Modifié le')
            ->hideOnForm();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Product) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Product) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        parent::updateEntity($entityManager, $entityInstance);
    }
}
