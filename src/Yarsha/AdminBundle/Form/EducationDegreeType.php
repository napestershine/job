<?php

namespace Yarsha\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EducationDegreeType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', null, [
                'label' => 'Name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Name'
                ]
            ])
            ->add('description', null, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description'
                ]
            ])
        ;

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\MainBundle\Entity\EducationDegree',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yarsha_admin_education_degree';
    }
}
