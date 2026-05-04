<?php

namespace App\Form;

use App\Entity\Usuario;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

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
            ->add('current_password', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Contraseña actual',
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all text-gray-800 bg-gray-50', 'autocomplete' => 'current-password'],
            ])
            //Campo nueva contraseña
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'invalid_message' => 'Las contraseñas nuevas deben coincidir.',
                'first_options'  => [
                    'label' => 'Nueva contraseña',
                    'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all text-gray-800 bg-gray-50', 'autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Repetir nueva contraseña',
                    'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all text-gray-800 bg-gray-50', 'autocomplete' => 'new-password'],
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Tu contraseña debe tener al menos {{ limit }} caracteres',
                        'max' => 4096,
                    ]),
                ],
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
