<?php

namespace App\Form;

use App\Entity\Comentario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ComentarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comentario', TextareaType::class, [
                'label'       => false,
                'attr'        => [
                    'placeholder' => 'Comparte tu experiencia sobre este viaje...',
                    'rows'        => 4,
                    'class'       => 'comentario-textarea',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'El comentario no puede estar vacío.']),
                    new Length(['max' => 255, 'maxMessage' => 'El comentario no puede superar los 255 caracteres.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comentario::class,
        ]);
    }
}
