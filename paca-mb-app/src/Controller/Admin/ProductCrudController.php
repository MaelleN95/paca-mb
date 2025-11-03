<?php
namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductImageType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
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
            ->add('category')
            ->add('isUsed');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Nom du produit');

        // Référence non modifiable
        yield TextField::new('reference', 'Référence')
            ->setFormTypeOption('disabled', true);

        yield SlugField::new('slug')
            ->setTargetFieldName('title')
            ->hideOnIndex();

        yield AssociationField::new('category', 'Catégorie');

        yield TextareaField::new('description', 'Description')
            ->hideOnIndex();

        yield MoneyField::new('price', 'Prix')
            ->setCurrency('EUR')
            ->setNumDecimals(2)
            ->hideOnIndex();

        yield BooleanField::new('isUsed', 'Produit d’occasion ?');

        yield CollectionField::new('productImages', 'Images')
            ->setEntryType(ProductImageType::class)
            ->setEntryIsComplex(true)
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->onlyOnForms();
                    
        // Afficher les miniatures dans l'index (liste)
        yield ImageField::new('firstImage', 'Image')
            ->setBasePath('/uploads/product_images/')
            ->onlyOnIndex();

        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm();

        yield DateTimeField::new('updatedAt', 'Modifié le')
            ->hideOnForm();
    }

    // Mettre à jour automatiquement updatedAt à la création
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Product) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        parent::persistEntity($entityManager, $entityInstance);
    }

    // Mettre à jour automatiquement updatedAt à la modification
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Product) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        parent::updateEntity($entityManager, $entityInstance);
    }
}
