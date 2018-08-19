<?php

namespace Yarsha\MainBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\MainBundle\Entity\Category;

class CategoryType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title')
            ->add('section', ChoiceType::class, [
                'choices' => array_flip(Category::$category_types)
            ])
            ->add('parent', EntityType::class, [
                'required' => false,
                'label' => 'Category',
                'class' => Category::class,
                'choice_label' => 'title',
                'placeholder' => '-- Select Category --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.published = 1')
                        ->andWhere('c.deleted = 0')
                        ->andWhere('c.parent is null');
                }
            ])
            ->add('Submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\MainBundle\Entity\Category'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_mainbundle_category';
    }

    public function getCategoryParents()
    {
        return [];
    }


}
