<?php

namespace App\Controller\Admin;

use App\Entity\Wallet;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class WalletCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Wallet::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
