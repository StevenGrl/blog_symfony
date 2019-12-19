<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Titre de l\'article',
                    'autofocus' => true
                ],
                'required' => false
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom de l\'auteur'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => [
                    'rows' => '4',
                    'placeholder' => 'Contenu de l\'article'
                ],
                'required' => false
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'Publié',
                'attr' => ['class' => 'custom-control-input'],
                'label_attr' => ['class' => 'custom-control-label'],
                'data' => true,
                'required' => false
            ])
            ->add('nbViews', IntegerType::class, [
                'label' => 'Nombre de vues'
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie(s)',
                'multiple' => true,
                'label_attr' => ['class' => 'ml-3'],
                'attr' => [
                    'class' => 'custom-select-lg',
                    'size' => '6'
                ],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
