<?php

namespace App\Controller\Admin;

use App\Entity\Viaje;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ViajeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Viaje::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Viaje')
            ->setEntityLabelInPlural('Viajes')
            ->setPageTitle(Crud::PAGE_INDEX, 'Gestión de Viajes')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Detalle del Viaje')
            ->setSearchFields(['titulo', 'destino'])
            ->setDefaultSort(['fecha_creacion' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Eliminar NEW y EDIT
            ->disable(Action::NEW, Action::EDIT)
            // Añadir el botón "Ver detalle" en el listado INDEX
            // (DETAIL y DELETE ya existen por defecto en la página DETAIL)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        // Campo de imagen de la primera imagen del viaje (para INDEX)
        // Se usa un campo de texto con la ruta de la imagen asociada al viaje
        // a través del getter de la colección de imágenes.

        // --- LISTADO (INDEX) ---
        if ($pageName === Crud::PAGE_INDEX) {
            return [
                IdField::new('id')->setLabel('ID'),
                TextField::new('titulo')->setLabel('Título'),
                TextField::new('destino')->setLabel('Destino'),
                IntegerField::new('duracion')->setLabel('Duración (días)'),
                NumberField::new('presupuesto')->setLabel('Presupuesto (€)'),
                AssociationField::new('id_usuario')->setLabel('Autor'),
                DateTimeField::new('fecha_creacion')->setLabel('Publicado el')->setFormat('dd/MM/yyyy'),
            ];
        }

        // --- VISTA DE DETALLE (DETAIL) ---
        // Muestra todos los atributos del viaje, incluyendo miniaturas de imágenes.
        if ($pageName === Crud::PAGE_DETAIL) {
            return [
                IdField::new('id')->setLabel('ID'),
                TextField::new('titulo')->setLabel('Título'),
                TextField::new('destino')->setLabel('Destino'),
                IntegerField::new('duracion')->setLabel('Duración (días)'),
                NumberField::new('presupuesto')->setLabel('Presupuesto (€)'),
                AssociationField::new('id_usuario')->setLabel('Autor'),
                DateTimeField::new('fecha_creacion')->setLabel('Publicado el')->setFormat('dd/MM/yyyy HH:mm'),
                TextareaField::new('contenido')->setLabel('Contenido')->renderAsHtml(false),
                TextareaField::new('alojamiento')->setLabel('Alojamiento'),
                TextareaField::new('gastronomia')->setLabel('Gastronomía'),
                // Imágenes del viaje: usa el getter virtual getImagenesUrls() que devuelve string[].
                // Las URLs ya son absolutas (/imagen/viaje/archivo.jpg), servidas por ImagenController.
                // Usamos un getter virtual en la entidad que devuelve la etiqueta img HTML en forma de string nativo.
                TextField::new('imagenesHtml')
                    ->setLabel('Imágenes')
                    ->onlyOnDetail()
                    ->renderAsHtml(),
            ];
        }

        // Fallback (no debería llegar aquí dado que NEW y EDIT están desactivados)
        return [
            IdField::new('id')->setLabel('ID'),
            TextField::new('titulo')->setLabel('Título'),
            TextField::new('destino')->setLabel('Destino'),
        ];
    }
}
