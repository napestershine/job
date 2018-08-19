<?php

namespace Yarsha\OrganizationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\OrganizationBundle\Entity\OrganizationContactPerson;

class OrganizationContactPersonType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $emailRequired = true;
        $phoneRequired = true;

        if ($options['contact_type'] == OrganizationContactPerson::CONTACT_TYPE_HEAD) {
            $emailRequired = false;
            $phoneRequired = false;
        }

        $builder
            ->add('firstName', null, [
                'attr' => [
                    'placeholder' => 'First Name',
                    'pattern' => '.{2,20}',
                    'title' => 'Minimum 2 and maximum 20 letters allowed'
                ]
            ])
            ->add('lastName', null, [
                'attr' => [
                    'placeholder' => 'Last Name',
                    'pattern' => '.{2,20}',
                    'title' => 'Minimum 2 and maximum 20 letters allowed'
                ]
            ])
            ->add('phone', TextType::class, [
                'required' => $phoneRequired,
                'attr' => [
                    'placeholder' => 'Phone Number',
                    'pattern' => '[0-9,]{5,20}',
                    'title' => 'Minimum 5 and maximum 20 numbers allowed'
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => $emailRequired,
                'attr' => [
                    'placeholder' => 'Email Address',
                    'class' => 'cont_email',
                    'pattern' => "[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"
                ]
            ])
            ->add('designation', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Designation'
                ]
            ])
            ->add('mobile', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Mobile Number'
                ]
            ])
            ->add('contactType', HiddenType::class, [
                'data' => $options['contact_type']
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\OrganizationBundle\Entity\OrganizationContactPerson',
            'contact_type' => OrganizationContactPerson::CONTACT_TYPE_OTHERS,
            'admin' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_organizationbundle_organizationcontactperson';
    }


}
