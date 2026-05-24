<?php

namespace App\Controller\Admin;

use App\Entity\Usuario;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class UsuarioCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Usuario::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Usuario')
            ->setEntityLabelInPlural('Usuarios')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestión de Usuarios')
            ->setPageTitle(Crud::PAGE_EDIT, 'Editar Usuario')
            ->setSearchFields(['nickname', 'email'])
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // EDIT ya existe por defecto en INDEX; solo hay que deshabilitar NEW y DELETE
            ->disable(Action::NEW, Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('baneado')->setLabel('Baneado'))
            ->add(TextFilter::new('nickname')->setLabel('Nickname'))
            ->add(TextFilter::new('email')->setLabel('Email'));
    }

    public function configureFields(string $pageName): iterable
    {
        // --- LISTADO (INDEX) ---
        if ($pageName === Crud::PAGE_INDEX) {
            return [
                IdField::new('id')->setLabel('ID'),
                TextField::new('nickname')->setLabel('Nickname'),
                EmailField::new('email')->setLabel('Email'),
                DateTimeField::new('fecha_registro')->setLabel('Fecha de Registro')->setFormat('dd/MM/yyyy HH:mm'),
                DateTimeField::new('deletedAt')->setLabel('Eliminado el')->setFormat('dd/MM/yyyy HH:mm'),
                BooleanField::new('baneado')
                    ->setLabel('Baneado')
                    ->renderAsSwitch(false), // Solo lectura en el listado
            ];
        }

        // --- FORMULARIO DE EDICIÓN (EDIT) ---
        // Solo el campo "baneado" es editable; el resto se muestra como solo lectura.
        return [
            IdField::new('id')
                ->setLabel('ID')
                ->setDisabled(true),
            TextField::new('nickname')
                ->setLabel('Nickname')
                ->setDisabled(true),
            EmailField::new('email')
                ->setLabel('Email')
                ->setDisabled(true),
            TextField::new('biografia')
                ->setLabel('Biografía')
                ->setDisabled(true),
            DateTimeField::new('fecha_registro')
                ->setLabel('Fecha de Registro')
                ->setDisabled(true)
                ->setFormat('dd/MM/yyyy HH:mm'),
            DateTimeField::new('deletedAt')
                ->setLabel('Eliminado el')
                ->setDisabled(true)
                ->setFormat('dd/MM/yyyy HH:mm'),
            // Único campo editable
            BooleanField::new('baneado')
                ->setLabel('¿Usuario baneado?')
                ->renderAsSwitch(true),
        ];
    }
}
