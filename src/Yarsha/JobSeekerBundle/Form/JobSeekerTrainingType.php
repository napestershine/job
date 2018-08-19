<?php

namespace Yarsha\JobSeekerBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobSeekerTrainingType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true
            ])
            ->add('year', NumberType::class, [
                'required' => true
            ])
            ->add('institution', TextType::class, [
                'required' => true
            ])
            ->add('duration', TextType::class, [
                'required' => true
            ])
            ->add('address', TextType::class, [
                'required' => true
            ])
            ->add('objective', CKEditorType::class, [
                'required' => true,
                'config_name' => 'simple_editor'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobSeekerBundle\Entity\JobSeekerTraining'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobseekerbundle_jobseekertraining';
    }


}
