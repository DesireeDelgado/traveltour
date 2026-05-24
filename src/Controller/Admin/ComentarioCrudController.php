<?php

namespace App\Controller\Admin;

use App\Entity\Comentario;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ComentarioCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comentario::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Comentario')
            ->setEntityLabelInPlural('Comentarios')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestión de Comentarios')
            ->setSearchFields(['comentario'])
            ->setDefaultSort(['fecha_creacion' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Solo se permiten INDEX y DELETE; se eliminan NEW y EDIT
            ->disable(Action::NEW, Action::EDIT)
            // Confirmar el borrado con un diálogo para evitar eliminaciones accidentales
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(static fn () => true);
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setLabel('ID'),
            AssociationField::new('id_usuario')
                ->setLabel('Usuario')
                ->formatValue(function ($value, $entity) {
                    return $entity->getIdUsuario()?->getNickname() ?? '—';
                }),
            AssociationField::new('id_viaje')
                ->setLabel('Viaje')
                ->formatValue(function ($value, $entity) {
                    return $entity->getIdViaje()?->getTitulo() ?? '—';
                }),
            TextareaField::new('comentario')
                ->setLabel('Comentario')
                ->setMaxLength(200),
            DateTimeField::new('fecha_creacion')
                ->setLabel('Fecha')
                ->setFormat('dd/MM/yyyy HH:mm'),
        ];
    }
}
