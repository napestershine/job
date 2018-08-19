<?php

namespace Yarsha\EmployerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployerLoginCredentialType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username', TextType::class, [
                'label' => 'Username',
                'attr' => [
                    'placeholder' => 'Username (Mobile/Email)',
                    'class' => 'form-usr-name'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'invalid_message' => 'The password fields must match.',
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\EmployerBundle\Entity\User',
            'admin' => false
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yarsha_employer_user_profile';
    }
}
