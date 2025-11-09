<?php

namespace App\Controller\Admin;

use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class NewsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return News::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Actualité')
            ->setEntityLabelInPlural('Actualités')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des actualités');
    }
        
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('publishedAt')
            ->add('expiresAt');
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');

        yield TextEditorField::new('content', 'Contenu')
            ->hideOnIndex();

        yield TextField::new('slug', 'slug')
            ->OnlyOnIndex();

        yield DateTimeField::new('publishedAt', 'Début de publication')
            ->setFormTypeOptions(['html5' => true]);

        yield DateTimeField::new('expiresAt', 'Fin de publication')
            ->setFormTypeOptions(['html5' => true]);

        yield ImageField::new('image', 'Image')
            ->setBasePath('/uploads/news')
            ->onlyOnIndex();

        yield TextField::new('imageFile', 'Image')
            ->setFormType(VichImageType::class)
            ->onlyOnForms();
    }

    public function createEntity(string $entityFqcn)
    {
        $news = new News();
       $news->setPublishedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
        return $news;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof News) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof News) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        parent::updateEntity($entityManager, $entityInstance);
    }
}
