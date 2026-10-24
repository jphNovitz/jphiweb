<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Blog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Translation\TranslatableMessage;

class BlogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Blog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setFormThemes(['@FOSCKEditor/Form/ckeditor_widget.html.twig', '@EasyAdmin/crud/form_theme.html.twig'])
            ->setEntityLabelInSingular('Blog')
            ->setEntityLabelInPlural('Blog')
            ->setDateFormat('long');
        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', new TranslatableMessage('Title'))->setColumns(12),
            TextField::new('seoTitle')
                ->setColumns('20')
                ->setLabel(new TranslatableMessage('SEO Title'))
                ->setHelp('Max 65 caractères')
                ->setFormTypeOption('attr', ['maxlength' => 65]),
            TextField::new('slugTitle')->setColumns('20')->setLabel(new TranslatableMessage('Slug Title')),
            TextField::new('summary', new TranslatableMessage('Summary'))->setColumns(12)
                ->setMaxLength(255),
            TextField::new('seoSummary', new TranslatableMessage('Meta Description'))->setColumns(12)
                ->setMaxLength(160),
            TextEditorField::new('content')
                ->setFormType(CKEditorType::class)
                ->setColumns('20')
                ->hideOnIndex()
            ->setTrixEditorConfig([
                'blockAttributes' => [
                    'default' => ['tagName' => 'p'],
                    'heading1' => ['tagName' => 'h3']
                ]
            ]),
            BooleanField::new('pin', new TranslatableMessage('Pin')),
            BooleanField::new('published', new TranslatableMessage('Published')),
            ImageField::new('image', new TranslatableMessage('Image'))
                ->setUploadDir('public/images/blog/')
                ->setBasePath('/images/blog/'),
            TextField::new('imageAlt', new TranslatableMessage('Image alt'))
                ->setColumns(12)
                ->setHelp('Texte alternatif de l’image'),
            AssociationField::new('topic', new TranslatableMessage('Topic'))
                ->setRequired(true)
                ->setColumns(12)
                ->setFormTypeOption('choice_label', 'title')
                ->setFormTypeOption('placeholder', 'Select a topic')
                ->setRequired(false)        // champ pas obligatoire
                ->setFormTypeOption('required', false) // le FormType Symfony devient nullable
                ->setFormTypeOption('placeholder', '— Aucun —'), // affichage d’une option vide
            AssociationField::new('relatedBlogs', new TranslatableMessage('Related Blogs'))
                ->setColumns(12)
                ->setFormTypeOption('choice_label', 'title')
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('expanded', false)

        ];
    }
//    public function configureFields(string $pageName): iterable
//    {
//        return [
//            IdField::new('id'),
//            TextField::new('title'),
//            TextEditorField::new('description'),
//        ];
//
//    }

}
