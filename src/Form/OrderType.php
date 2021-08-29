<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityRepository;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', EntityType::class, [
            'class' => Product::class,
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => false
            ])
            ->add('Quantity', IntegerType::class)
            ->add('CustomerName', TextType::class)
            ->add('CustomerAddress', TextType::class)
            ->add('Phone')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}