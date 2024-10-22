<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag; // add
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use function Symfony\Component\Clock\now;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => ' flex flex-col input input-bordered w-full mb-5',
                ]
            ])
            ->add('intro', TextareaType::class, [
                'attr' => [
                    'class' => ' flex flex-col textarea textarea-bordered w-full h-[150px] mb-5',
                ]
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => ' flex flex-col textarea textarea-bordered w-full h-[250px] mb-5',
                ]
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime('now'),
                'required' => true,
                'label' => false,
                'attr' => [
                    'class' => 'hidden',
                    'readonly' => true,
                ]
            ])
            ->add('imageFile', FileType::class, [
                'attr' => [
                    'class' => 'file-input file-input-bordered file-input-primary w-full mb-3',
                    'placeholder' => 'Download an image'
                ],
                'label' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('liked', null, [
                'label' => false,
                'attr' => [
                    'class' => 'hidden',
                    'disabled' => true,
                ]
            ])
            ->add('category', EntityType::class, [
                // 'label' => false,
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => ' select select-bordered w-full justify-center items-center mb-5',
                ]
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'flex flex-row mb-5 h-fit gap-4'
                ]
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
