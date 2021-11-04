<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType ;
use Symfony\Component\Form\Extension\Core\Type\IntegerType ;
use Symfony\Component\Form\Extension\Core\Type\ButtonType ;

class MicroSmsConfigDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('netprice', MoneyType::class, ['currency' => 'PLN'])
            ->add('number', IntegerType::class)
            ->add('amount', MoneyType::class, ['currency' => 'PLN'])
            ->add('name', TextType::class)
            ->add('product', TextType::class)
            ->add('delete', ButtonType::class, ['attr' => ['class' => 'remove-collection-btn']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'netprice' => 0.00,
            // 'number' => 0,
            // 'amount' => 0.00,
            // 'name' => '',
            // 'product' => ''
        ]);
        // $resolver->setAllowedTypes('netprice', 'float');
        // $resolver->setAllowedTypes('number', 'int');
        // $resolver->setAllowedTypes('amount', 'float');
        // $resolver->setAllowedTypes('name', 'string');
        // $resolver->setAllowedTypes('product', 'string'); 
    }
}
