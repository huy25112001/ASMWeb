<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name')
            ->add('Description')
            ->add('Price', MoneyType::class,
            [
            'currency' => "USD"
            ])
            ->add('Category', EntityType::class,
            [
                'class' => Category::class,
                'choice_label' => "name"
            ])
            -> add('Image', FileType::class,
            [
                // 'label' => 'Product Image',
                'data_class' => null,
                'required'=> is_null($builder->getData()->getImage())
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            
        ]);
    }
}
