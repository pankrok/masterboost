<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [];
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $fields = [
                IdField::new('id')->onlyOnIndex(),
                EmailField::new('email')->setFormTypeOption('disabled', true),
                TextField::new('login')->setFormTypeOption('disabled', true),
                NumberField::new('wallet'),
                BooleanField::new('banned'),
                BooleanField::new('isVerified'),
                AssociationField::new('servers')->onlyOnIndex()
            ];
        }
        
        if ($this->isGranted('ROLE_SUPERADMIN')) {
            $fields = [
                IdField::new('id')->onlyOnIndex(),
                EmailField::new('email'),
                TextField::new('login'),
                TextField::new('password')->hideOnIndex()
                    ->setFormType(PasswordType::class)
                    ->setFormTypeOptions(['required' => false, 'empty_data' => '']),
                CollectionField::new('roles')->hideOnIndex()->setFormTypeOptions([
                    'entry_type'   => ChoiceType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options'  => [
                        'choices'  => [
                            'ROLE_USER' => 'ROLE_USER',
                            'ROLE_MODERATOR'    => 'ROLE_MODERATOR',
                            'ROLE_ADMIN'     => 'ROLE_ADMIN',
                            'ROLE_SUPERADMIN'    => 'ROLE_SUPERADMIN',
                        ],
                    ]]),
                NumberField::new('wallet'),
                BooleanField::new('banned'),
                BooleanField::new('isVerified'),
                AvatarField::new('avatar')->hideOnIndex(),
                AssociationField::new('servers')->onlyOnIndex()
            ];
        }
        
       
        
        return $fields;
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('login')
            ->add('email')
            ->add('wallet')
            ->add('banned')
            ->add('isVerified')
        ;
    }

    
    public function createEntity(string $entityFqcn)
    {
        $user = new User();
        $user->setAvatar('');
        
        return $user;
    }
}
