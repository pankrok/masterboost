<?php

namespace App\Controller\Admin;

use App\Entity\Servers;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\OptionsResolver\OptionsResolver;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class ServersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Servers::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('address')->setLabel('IP and port address'),
            TextField::new('hostname')->onlyOnIndex(),
            NumberField::new('rate'),
            NumberField::new('mainpage'),
            AssociationField::new('uid')->setLabel('Username'),
            DateField::new('date_end_dynamic'),
            DateField::new('date_end_static'),
            ChoiceField::new('type')->setChoices([
                        'No Boost' => 0,
                        'Static' => 1,
                        'Dynamic' => 2
                    ]),
        ];
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('address')
            ->add('uid')
            ->add('rate')
            ->add('mainpage')
            ->add('status')
            ->add('hostname')
            ->add('date_end_static')
            ->add('date_end_dynamic')
            ->add('type')
        ;
    }
    
    public function createEntity(string $entityFqcn)
    {
        $server = new Servers();
        $server->setStatus(true);
        $server->setGame('cs16');
        
        
        return $server;
    }
    
}
