<?php

namespace Yarsha\EmployerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Yarsha\OrganizationBundle\Entity\OrganizationAutoEmailResponder;


class AutoEmailResponseType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('autoresponsetext', CKEditorType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('autoresponse', ChoiceType::class, [
                'choices' => array_flip(OrganizationAutoEmailResponder::$emailResponseStatus),
                'expanded' => true,
                'multiple' => false
            ]);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\OrganizationBundle\Entity\OrganizationAutoEmailResponder',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'yarsha_employer_auto_email_responder';
    }
}
