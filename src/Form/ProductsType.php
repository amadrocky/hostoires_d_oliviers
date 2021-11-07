<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('mainDescription')
            ->add('price')
            ->add('originPrice')
            ->add('mainAttribute1')
            ->add('mainAttribute2')
            ->add('mainAttribute3')
            ->add('pictures')
            ->add('qrCode')
            ->add('descriptionShow1')
            ->add('descriptionShow2')
            ->add('descriptionShow3')
            ->add('createdAt')
            ->add('modifiedAt')
            ->add('workflowState')
            ->add('category')
            ->add('tax')
            ->add('orders')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
