<?php

namespace App\Form;

use App\Entity\Usuario;
use App\Entity\Viaje;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ViajeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo')
            ->add('destino')
            ->add('duracion')
            ->add('presupuesto')
            ->add('contenido')
            ->add('alojamiento')
            ->add('gastronomia')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Viaje::class,
        ]);
    }
}
