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
            ->add('fecha_creacion', null, [
                'widget' => 'single_text',
            ])
            ->add('contenido')
            ->add('id_usuario', EntityType::class, [
                'class' => Usuario::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Viaje::class,
        ]);
    }
}
