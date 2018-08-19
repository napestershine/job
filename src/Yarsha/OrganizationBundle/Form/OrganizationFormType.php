<?php

namespace Yarsha\OrganizationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('description')
            ->add('logo')
            ->add('profile')
            ->add('address')
            ->add('fax')
            ->add('website')
            ->add('postBox')
            ->add('email')
            ->add('secondaryEmail')
            ->add('profileStatus')
            ->add('status')
            ->add('createdDate')
            ->add('sortOrder')
            ->add('accessCode')
            ->add('externalLink')
            ->add('updatedDate')
            ->add('visit')
            ->add('nature')
            ->add('label')
            ->add('type')
            ->add('category')
            ->add('ownershipType')
            ->add('size')
            ->add('country')
            ->add('approvedBy')
            ->add('settings')        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yarsha\OrganizationBundle\Entity\Organization'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_organizationbundle_organization';
    }


}
