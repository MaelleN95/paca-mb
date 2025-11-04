<?php
namespace App\Controller\Admin;

use App\Entity\ToolType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ToolTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ToolType::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Type d’outil')
            ->setEntityLabelInPlural('Types d’outils')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', 'Nom');
    }
}
