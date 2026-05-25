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
            ->add('gastronomia');
            
        if (!$options['is_edit']) {
            $builder->add('imagenes', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'required' => true,
                'label' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Count([
                        'min' => 1,
                        'max' => 5,
                        'minMessage' => 'Debes subir al menos una imagen (será la portada).',
                        'maxMessage' => 'No puedes subir más de 5 imágenes en total.'
                    ]),
                    new \Symfony\Component\Validator\Constraints\All([
                        'constraints' => [
                            new \Symfony\Component\Validator\Constraints\Image([
                                'maxSize' => '5M',
                                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'],
                                'mimeTypesMessage' => 'Por favor, sube imágenes válidas (JPG, PNG, WEBP)',
                            ])
                        ]
                    ])
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Viaje::class,
            'csrf_protection' => false, // <-- AÑADE ESTO
            'is_edit' => false,
        ]);
    }
}
