<?php

namespace App\Controller\Admin;

use App\Entity\PromoCodes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class PromoCodesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PromoCodes::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('code')
            ->add('amount')
            ->add('used')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setFormTypeOptions(['disabled' => true, 'data' => null]),
            TextField::new('code'),
            NumberField::new('amount'),
            AssociationField::new('used')->setFormTypeOptions(['disabled' => true, 'data' => null]),
        ];
    }
    
    public function deleteEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, $entityInstance):void
    {
        $this->addFlash('danger', 'No one can delete Promo Codes!');
    }

}
