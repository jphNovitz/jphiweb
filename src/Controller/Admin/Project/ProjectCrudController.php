<?php

namespace App\Controller\Admin\Project;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Translation\TranslatableMessage;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setFormThemes(['@FOSCKEditor/Form/ckeditor_widget.html.twig', '@EasyAdmin/crud/form_theme.html.twig']);
        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title')->setColumns('20'),
            TextField::new('seoTitle')
                ->setColumns('20')
                ->setLabel(new TranslatableMessage('SEO Title'))
                ->setHelp('Max 65 caractères')
                ->setFormTypeOption('attr', ['maxlength' => 65]),
            TextField::new('slugTitle')->setColumns('20')->setLabel(new TranslatableMessage('Slug Title')),
            TextField::new('seoSummary', new TranslatableMessage('Meta Description'))->setColumns(12)
                ->setMaxLength(160),
            TextField::new('header')->setColumns('20'),
            TextField::new('link'),
            BooleanField::new('published', new TranslatableMessage('Published')),
            TextEditorField::new('description')
                ->setFormType(CKEditorType::class)
                ->setColumns('20')
                ->hideOnIndex(),
            AssociationField::new('category', 'Category'),
            AssociationField::new('tags', 'Tags')
                ->setFormTypeOptions([
                    'by_reference' => false, // Nécessaire pour ManyToMany
                ])
                ->autocomplete(), // Ajoute une recherche auto-complétée pour améliorer l'expérience utilisateur
            AssociationField::new('technologies', 'Technologies')
                ->setFormTypeOptions([

                    'by_reference' => false, // Nécessaire pour gérer ManyToMany
                ])
                ->autocomplete(),
            ImageField::new('image')
                ->setUploadDir('public/images/projets')
                ->setBasePath('/images/projets')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
        ];
    }

}
