<?php

namespace App\Controller\Admin;

use App\Entity\Comments;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;

class CommentsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comments::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $return = [];
        
        if ($this->isGranted('ROLE_MODERATOR')) {
            $return = [
                IdField::new('id')->onlyOnIndex(),
                BooleanField::new('accept'),
                TextField::new('comment')->setFormTypeOption('disabled', true),
                AssociationField::new('uid')->setFormTypeOption('disabled', true),
                AssociationField::new('sid')->setFormTypeOption('disabled', true),
            ];
        }
        
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $return = [
                IdField::new('id')->onlyOnIndex(),
                BooleanField::new('accept'),
                TextEditorField::new('comment'),
                AssociationField::new('uid'),
                AssociationField::new('sid'),
            ];
        }
        
        return $return;
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add(EntityFilter::new('uid'))
            ->add(EntityFilter::new('sid'))
            ->add('accept')
            ->add('comment')
       ;
    }

}
