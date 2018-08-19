<?php

namespace Yarsha\EmployerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordResetType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'options' => ['translation_domain' => 'EmployerBundle'],
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm Password'],
                    'invalid_message' => 'Password Mismatched',
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\EmployerBundle\Entity\User',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yarsha_employer_reset_password';
    }
}
