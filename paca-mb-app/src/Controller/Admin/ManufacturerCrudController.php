<?php
namespace App\Controller\Admin;

use App\Entity\Manufacturer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ManufacturerCrudController extends AbstractCrudController
{
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
        yield TextField::new('slug')->hideOnForm();
        yield UrlField::new('webSite', 'Site web')->hideOnIndex();
        yield ImageField::new('logo')->setBasePath('/uploads/manufacturers/');
    }
}
