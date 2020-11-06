<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Введите Ваше Имя'
                ]
            ])
            ->add('text', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Оставьте Комментарий'
                ]
            ])
            ->add('mediaLink', FileType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/pjpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid jpeg, jpg, png file',
                    ])
                ],
                'help' => '<i class="fa fa-info-circle" aria-hidden="true"></i> Поддерживаемые форматы jpeg, jpg, png',
                'help_html' => true
            ])
            ->add('type', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => Comment::getTypes(true),
                'placeholder' => '--- Выбрать ---'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'comment';
    }
}
