<?php

namespace App\Form;

use App\Entity\Usuario;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', CollectionType::class)
            ->add('password')
            ->add('tipo')
            ->add('nickname')
            ->add('biografia')
            ->add('fecha_registro', null, [
                'widget' => 'single_text',
            ])
            ->add('url_foto_perfil')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
