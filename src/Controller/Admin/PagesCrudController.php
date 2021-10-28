<?php

namespace App\Controller\Admin;

use App\Entity\Pages;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\LocaleField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class PagesCrudController extends AbstractCrudController
{    
    public static function getEntityFqcn(): string
    {
        return Pages::class;
    }
        
    public function configureFields(string $pageName): iterable
    {        
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('prefix')->setFormTypeOptions(['required' => false]),
            TextField::new('name'),
            LocaleField ::new('locale')->setFormTypeOptions(['choice_loader' => null, 'choices' => [
                        'English' => 'en',
                        'Polish' => 'pl',
                        'Russian' => 'ru'
                    ]]),
            NumberField::new('pageOrder'),
            BooleanField::new('active'),
            TextEditorField::new('content')
        ];
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ->overrideTemplate(
                // 'crud/field/text_editor','admin/crud/field/text_editor.html.twig'
            // )
        ;
    }
   
}
