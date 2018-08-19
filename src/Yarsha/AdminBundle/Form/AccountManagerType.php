<?php

namespace Yarsha\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class AccountManagerType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', null, [
                'label' => 'Full Name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Full Name'
                ]
            ])
            ->add('designation', null, [
                'label' => 'Designation',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Designation'
                ]
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'Contact Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Contact Email'
                ]
            ])
            ->add('phone', null, [
                'label' => 'Phone Number',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Phone Number'
                ]
            ])
            ->add('mobile', null, [
                'label' => 'Mobile Number',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Mobile Number'
                ]
            ])
            ->add('address', null, [
                'label' => 'Address',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Address'
                ]
            ])
            ->add('file', FileType::class, [
                'label' => 'Profile Picture',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Mobile Number'
                ]
            ]);
        if ($options['is_updating'] === false) {
            $builder
                ->add('username', null, [
                    'label' => 'Username',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Username'
                    ]
                ])->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required' => true,
                    'invalid_message' => 'Password fields must match.',
                    'first_options' => [
                        'label' => 'Password',
                        'attr' => [
                            'placeholder' => 'Password'
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Confirm Password',
                        'attr' => [
                            'placeholder' => 'Confirm Password'
                        ]
                    ]
                ]);
        }

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\AdminBundle\Entity\User',
            'is_updating' => false
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yarsha_admin_account_manager';
    }
}
