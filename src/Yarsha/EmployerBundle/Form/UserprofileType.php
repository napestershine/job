<?php

namespace Yarsha\EmployerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserprofileType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username')
            ->add('email', EmailType::class);
//            ->add('file', FileType::class, [
//                'label' => 'Company Logo',
//                'required' => false,
//                'mapped' => false,
//                'attr' => [
//                    'class' => 'btn btn-default btn-file image-upload',
//                    'onchange' => 'previewFile();',
//                    'data-provides' => 'fileinput',
//                ]
//            ]
//            );
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
        return 'yarsha_employer_user_profile';
    }
}
