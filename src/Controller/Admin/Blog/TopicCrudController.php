<?php

namespace App\Controller\Admin\Blog;

use App\Entity\Topic;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Translation\TranslatableMessage;

class TopicCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Topic::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setFormThemes(['@FOSCKEditor/Form/ckeditor_widget.html.twig', '@EasyAdmin/crud/form_theme.html.twig']);
        return $crud;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title')->setColumns('20')->setLabel('Titre'),
            TextField::new('seoTitle')
                ->setColumns('20')
                ->setLabel(new TranslatableMessage('SEO Title'))
                ->setHelp('Max 65 caractères')
                ->setFormTypeOption('attr', ['maxlength' => 65]),
            TextField::new('slugTitle')->setColumns('20')->setLabel(new TranslatableMessage('Slug Title')),
            TextField::new('summary')->setColumns('20'),
            TextField::new('seoSummary', new TranslatableMessage('Meta Description'))->setColumns(12)
                ->setMaxLength(160),
            TextEditorField::new('content')
                ->setFormType(CKEditorType::class)
                ->setColumns('20')
                ->hideOnIndex(),
            ImageField::new('image', new TranslatableMessage('Image'))
                ->setUploadDir('public/images/topic/')
                ->setBasePath('/images/topic/'),
            TextField::new('imageAlt', new TranslatableMessage('Image alt'))
                ->setColumns(12)
                ->setHelp('Texte alternatif de l’image'),
            BooleanField::new('published', new TranslatableMessage('Published')),
            AssociationField::new('parentTopic', new TranslatableMessage('ParentTopic'))
                ->setColumns(12)
                ->setFormTypeOption('choice_label', 'title')
                ->setFormTypeOption('placeholder', 'Select a topic')
                ->setRequired(false)        // champ pas obligatoire
                ->setFormTypeOption('required', false) // le FormType Symfony devient nullable
                ->setFormTypeOption('placeholder', '— Aucun —'),
            AssociationField::new('relatedBlogs', new TranslatableMessage('Related Blogs'))
                ->setColumns(12)
                ->setFormTypeOption('choice_label', 'title')
                ->setFormTypeOption('multiple', true)
                ->setFormTypeOption('by_reference', false)
                ->setFormTypeOption('placeholder', 'Select related blogs')
        ];
    }

}
