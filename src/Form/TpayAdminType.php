<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TpayAdminType extends AbstractType
{

    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active', ChoiceType::class, ['choices' => [
                    'yes' => true,
                    'no' => false,
                ],'data' => intval($options['active'])])
            ->add('merchantId', NumberType::class, ['data' => intval($options['merchantId'])])
            ->add('merchantSecret', TextType::class, ['data' => $options['merchantSecret']])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'active' => 0,
            'merchantId' => 1010,
            'merchantSecret' => 'merchantSecret',
        ]);
        
        $resolver->setAllowedTypes('active', 'boolean');
        $resolver->setAllowedTypes('merchantId', 'int');
        $resolver->setAllowedTypes('merchantSecret', 'string');
    }
}
