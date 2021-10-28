<?php

namespace App\Controller\Admin;

use App\Entity\Vote;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class VoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Vote::class;
    }

    public function configureFields(string $pageName): iterable
    {        
        if ($this->isGranted('ROLE_ADMIN')) {
            $fields = [
                IdField::new('id')->onlyOnIndex(),
                TextField::new('vote_ip'),
                AssociationField::new('user'),
                AssociationField::new('sid'),
                DateField::new('createdAt')->setFormTypeOptions(['disabled' => true, 'empty_data' => null]),
            ];
        }
        
       
        
        return $fields;
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('sid')
            ->add('user')
            ->add('vote_ip')
            ->add('createdAt')
        ;
    }
}
