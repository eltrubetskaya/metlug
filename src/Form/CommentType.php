<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('mediaLink', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'https://...'
                ],
                'help' => '<i class="fa fa-info-circle" aria-hidden="true"></i> Здесь можно добавить прямую ссылку на файл',
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
