<?php

namespace Yarsha\TagsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TagType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('slug', TextType::class, [
                'required' => false
            ]);

        if( $options['add'] == false )
        {
            $builder->add('changeTo', EntityType::class, [
                'mapped' => false,
                'required' => false,
                'class' => 'Yarsha\TagsBundle\Entity\Tag',
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'tagSelectAutoComplete form-control'
                ]
            ]);
        }

    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yarsha\TagsBundle\Entity\Tag',
            'add' => false
        ));
    }
}
