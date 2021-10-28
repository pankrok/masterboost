<?php

namespace App\Form;

use App\Entity\Servers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType ;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class DynamicAdType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('credits', NumberType::class, [
                'mapped' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'ad cost cannot be blank',
                    ])
                ]])
            ->add('days', NumberType::class, [
                'mapped' => false, 
                'constraints' => [
                    new NotBlank([
                        'message' => 'you have to set time',
                    ])]])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Servers::class,
        ]);
    }
}
