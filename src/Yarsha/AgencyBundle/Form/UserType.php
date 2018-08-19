<?php

namespace Yarsha\AgencyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('email')
            ->add('address')->add('phone')->add('website')
            ->add('contactPersonName')
            ->add('contactPersonEmail')
            ->add('contactPersonPhone');


        if ($options['is_updating'] === false) {
            $builder
                ->add('username', null, [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Username',
                        'class' => 'form-usr-name',
                    ]
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required' => true,
                    'invalid_message' => 'Password fields must match.',
                    'first_options' => [
                        'label' => 'Password',
                        'attr' => [
                            'placeholder' => 'Password',
                            'class' => 'pass'
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Confirm Password',
                        'attr' => [
                            'placeholder' => 'Confirm Password',
                            'class' => 'con_pass'
                        ]
                    ]
                ]);
        }

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yarsha\AgencyBundle\Entity\User',
            'is_updating' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_agencybundle_user';
    }


}
