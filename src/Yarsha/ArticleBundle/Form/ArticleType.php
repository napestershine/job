<?php

namespace Yarsha\ArticleBundle\Form;

use FM\ElfinderBundle\Form\Type\ElFinderType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\ArticleBundle\Entity\Article;

class ArticleType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('category', ChoiceType::class, [
                'choices' => array_flip(Article::$articleCategories)
            ])
            ->add('content', CKEditorType::class, [
//                'config_name' => 'my_config'
            ])
            ->add('image', ElFinderType::class, [
                'required' => false,
                'instance' => 'form',
                'attr' => [
                    'placeholder' => 'Click here to browse image',
                    'class' => 'form-control featuredImageBrowse',
                    'readonly' => 'readonly',
                    'data-trigger' => 'elfinder'
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array_flip(Article::$articleStatusOptions),
                'expanded' => true,
                'multiple' => false
            ])
            ->add('metaKeywords')
            ->add('metaDescriptions')
            ->add('metaTags')
            ->add('tags', TextType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'data-provide' => 'typeahead',
                    'class' => 'form-control tags-autocomplete'
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\ArticleBundle\Entity\Article'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_articlebundle_article';
    }


}
