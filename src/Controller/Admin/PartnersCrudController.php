<?php

namespace App\Controller\Admin;

use App\Entity\Partners;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class PartnersCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Partners::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            BooleanField::new('active'),
            TextField::new('name'),
            TextField::new('url'),
            ImageField::new('image')
            ->setBasePath('uploads/')
            ->setUploadDir('public/uploads')
            // ->setFormType(FileType::class)
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false),
            DateTimeField::new('updatedAt')->setFormTypeOptions(['disabled' => false, 'data' => new \DateTimeImmutable()]),
            DateTimeField::new('createdAt')->setFormTypeOptions(['disabled' => false, 'data' => new \DateTimeImmutable()])->hideWhenUpdating()
        ];
    }
    
}
