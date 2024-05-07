<?php
// src/Form/PostType.php
namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Ваше имя'
            ])
            ->add('title', TextType::class, [
                'label' => 'Название'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Текст'
            ])
            ->add('photo', FileType::class, [
                'label' => 'Фото (если есть)',
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Опубликовать'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null, 
        'csrf_protection' => true,
        ]);
    }
}
