<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Doctrine\Common\Collections\ArrayCollection;

class MicroSmsConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        

        // Reindex collection using id
        $indexedCollection = new ArrayCollection();
        foreach ($options['sms'] as $k => $collectionItem) {
            $indexedCollection->set($k, $collectionItem);
        }
        
        $builder
            ->add('active', CheckboxType::class, ['data' => $options['active']])
            ->add('userid', IntegerType::class,  [
                'attr' => [
                    'min' => 1,
                    'max' => 99999
                ],
                'data' => $options['userid']
                ])
            ->add('serviceid', IntegerType::class,  [
                'attr' => [
                    'min' => 1,
                    'max' => 99999
                ],
                'data' => $options['serviceid']
                ])
            ->add('text', TextType::class, ['data' => $options['text']])
            ->add('sms', CollectionType::class, [
                'entry_type'   => MicroSmsConfigDataType::class, 
                'allow_add' => true,
                'prototype' => true,
                'data' => $indexedCollection,
                'entry_options' => [
                    'label' => false, 
                    'attr' => ['class' => 'row'],
            ]])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'active' => true,
            'userid' => 1,
            'serviceid' => 1,
            'text' => 'EXAM.PL10',
            'sms' => [],
        ]);
        $resolver->setAllowedTypes('active', 'bool');
        $resolver->setAllowedTypes('userid', 'int');
        $resolver->setAllowedTypes('serviceid', 'int');
        $resolver->setAllowedTypes('text', 'string');
        $resolver->setAllowedTypes('sms', 'array'); 
    }
}
