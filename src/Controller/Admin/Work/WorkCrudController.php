<?php

namespace App\Controller\Admin\Work;

use App\Entity\Work;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Translation\TranslatableMessage;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Enum\WorkType;

class WorkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Work::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Service')
            ->setEntityLabelInPlural('Service')
            ->setDateFormat('long')
            ->setFormThemes(['@FOSCKEditor/Form/ckeditor_widget.html.twig', '@EasyAdmin/crud/form_theme.html.twig'])
//            ->setDateIntervalFormat('%%d Day(s) %%m Month(s) %%y Year(s)')
//            ->setPageTitle('index', '%entity_label_plural% listing')
            // ...
            ;
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
//            IdField::new('id'),
            TextField::new('name', new TranslatableMessage('Name'))->setColumns(12)
                ->setFormTypeOption('attr', ['maxlength' => 255]),
            TextField::new('seoTitle')
                ->setColumns('20')
                ->setLabel(new TranslatableMessage('SEO Title'))
                ->setHelp('Max 65 caractères')
                ->setFormTypeOption('attr', ['maxlength' => 65]),
            TextField::new('slugTitle')->setColumns('20')->setLabel(new TranslatableMessage('Slug Title'))->setFormTypeOption('attr', ['maxlength' => 65]),
            TextField::new('slug', 'Slug')
                ->onlyOnDetail(),
            TextField::new('seoDescription', new TranslatableMessage('Meta Description'))->setColumns(12)
                ->setMaxLength(160)->setFormTypeOption('attr', ['maxlength' => 160]),
            TextField::new('description_short', new TranslatableMessage('Description_short')),
            TextEditorField::new('description_long', new TranslatableMessage('Description_long'))
                ->setFormType(CKEditorType::class)
                ->hideOnIndex()
                ->setColumns(80),
            IntegerField::new('price', new TranslatableMessage('Price')),
            BooleanField::new('active', new TranslatableMessage('Active')),
            BooleanField::new('front', new TranslatableMessage('Front')),
            BooleanField::new('showStartingFrom', new TranslatableMessage('Mention « À partir de »')),
            BooleanField::new('showHostingNote', new TranslatableMessage('Mention hébergement et nom de domaine')),
            TextField::new('priceNote', new TranslatableMessage('Note de prix'))
                ->setHelp('Précision affichée sous le prix, ex. : Amélioration sur devis')
                ->setFormTypeOption('attr', ['maxlength' => 255])
                ->hideOnIndex(),
            ImageField::new('image', new TranslatableMessage('Image'))
                ->setUploadDir('public/images/work/')
                ->setBasePath('public/images/work/'),
            ChoiceField::new('type')
                ->setChoices(WorkType::cases())
                ->setFormTypeOption('choice_label', fn (WorkType $choice) => $choice->name)
                ->setFormTypeOption('choice_value', fn (?WorkType $choice) => $choice?->value)
                ->setFormTypeOption('placeholder', '—'),
            TextField::new('demoUrl', new TranslatableMessage('DemoUrl')),

        ];
    }

}
