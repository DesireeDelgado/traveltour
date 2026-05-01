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
            ->add('nickname', null, [
                'label' => 'Nickname',
                'attr' => ['placeholder' => 'Tu alias único', 'class' => 'w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all font-body-md text-gray-800 bg-gray-50']
            ])
            ->add('biografia', null, [
                'label' => 'Biografía',
                'attr' => ['placeholder' => 'Cuéntanos sobre tu estilo de viaje...', 'rows' => 5, 'class' => 'w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all font-body-md text-gray-800 bg-gray-50 resize-none']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
