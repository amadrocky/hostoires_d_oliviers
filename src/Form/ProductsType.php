<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Categories;
use App\Entity\Taxes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom (titre)'
                ]
            ])
            ->add('mainDescription', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Description principale'
                ]
            ])
            ->add('price', IntegerType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prix'
                ]
            ])
            ->add('originPrice', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prix d\'origine (si promotion)'
                ]
            ])
            ->add('mainAttribute1', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Attribut 1'
                ]
            ])
            ->add('mainAttribute2', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Attribut 2'
                ]
            ])
            ->add('mainAttribute3', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Attribut 3'
                ]
            ])
            ->add('descriptionShow1', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Texte descriptif 1'
                ]
            ])
            ->add('descriptionShow2', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Texte descriptif 2'
                ]
            ])
            ->add('descriptionShow3', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Texte descriptif 3'
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => false,
                'required' => true,
                'class' => Categories::class,
                'choice_label' => 'name',
                'placeholder' => 'Séléctionnez une catégorie'
            ])
            ->add('tax', EntityType::class, [
                'label' => false,
                'required' => true,
                'class' => Taxes::class,
                'choice_label' => 'name',
                'placeholder' => 'Séléctionnez une taxe'
            ])
            ->add('quantity', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Quantité',
                    'min' => 0
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
