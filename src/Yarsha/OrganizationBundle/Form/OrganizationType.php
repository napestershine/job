<?php

namespace Yarsha\OrganizationBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', CKEditorType::class, [])
            ->add('profile')
            ->add('address')
            ->add('fax')
            ->add('phone')
            ->add('website')
            ->add('postBox')
            ->add('email')
            ->add('secondaryEmail')
            ->add('profileStatus')
            ->add('status')
            ->add('sortOrder')
            ->add('accessCode')
            ->add('externalLink')
            ->add('nature')
            ->add('label')
            ->add('categoryType')
            ->add('isNewspaperOrganization')
            ->add('isGovermentOrganization')
            ->add('path')
            ->add('coverpath')
            ->add('type')
            ->add('category')
            ->add('industry')
            ->add('ownershipType')
            ->add('size')
            ->add('country')
            ->add('country')
            ->add('settings')
            ->add('accountManager');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\OrganizationBundle\Entity\Organization'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_organizationbundle_organization';
    }


}
