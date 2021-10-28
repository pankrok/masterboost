<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType ;
use Symfony\Component\Form\Extension\Core\Type\TextType ;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    { 
        $builder
            ->add('maxStatic', NumberType::class, ['data' => intval($options['maxStatic'])])
            ->add('staticCostPerDay', NumberType::class, ['data' => $options['staticCostPerDay']])
            ->add('maxDynamic', NumberType::class, ['data' => $options['maxDynamic']])
            ->add('minRate', NumberType::class, ['data' => $options['minRate']])
            ->add('minDays', NumberType::class, ['data' => $options['minDays']])
            ->add('minCredits', NumberType::class, ['data' => $options['minCredits']])
            ->add('termUrl', TextType::class, ['data' => $options['termUrl']])
            // ->add('maxGraphic', NumberType::class, ['data' => $options['maxGraphic']])
            // ->add('maxPromo', NumberType::class, ['data' => $options['maxPromo']])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'maxStatic' => 20,
            'staticCostPerDay' => 100,
            'maxDynamic' => 10,
            'maxGraphic' => 10,
            'maxPromo' => 10,
            'minDays' => 1,
            'minCredits' => 1,
            'minRate' => 0.01,
            'termUrl' => ''
        ]);
        $resolver->setAllowedTypes('maxStatic', 'int');
        $resolver->setAllowedTypes('staticCostPerDay', 'int');
        $resolver->setAllowedTypes('maxDynamic', 'int');
        $resolver->setAllowedTypes('minRate', 'float');
        $resolver->setAllowedTypes('minDays', 'int');
        $resolver->setAllowedTypes('minCredits', 'int');
        $resolver->setAllowedTypes('maxGraphic', 'int');
        $resolver->setAllowedTypes('maxPromo', 'int');
        $resolver->setAllowedTypes('termUrl', 'string');
    }
}
