<?php

namespace App\Form;

use App\Entity\Servers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddServerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class, [
                'attr' => ['pattern' => '^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5]):[0-9][0-9][0-9][0-9][0-9]$']
            ])
            ->add('game', ChoiceType::class, ['choices'  => [
                    'Counter Strike 1.6' => 'cs16',
                    // 'Counter Strike Source' => 'css',
                ]])
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
