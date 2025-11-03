<?php
namespace App\Controller\Admin;

use App\Entity\News;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class NewsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return News::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Actualités')
            ->setEntityLabelInSingular('Actualité')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestion des actualités')
            ->setDefaultSort(['publishedAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield SlugField::new('slug')->setTargetFieldName('title');
        yield TextareaField::new('content', 'Contenu')->hideOnIndex();
        yield ImageField::new('image', 'Image')
            ->setBasePath('uploads/news')
            ->setUploadDir('public/uploads/news')
            ->setRequired(false);
        yield DateTimeField::new('publishedAt', 'Date de publication');
        yield BooleanField::new('isActive', 'Publiée ?');
    }
}
