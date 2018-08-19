<?php

namespace Yarsha\JobSeekerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class JobSeekerLanguageType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = ['EXCELLENT' => 'excellent', 'GOOD' => 'good', 'AVERAGE' => 'average', 'POOR' => 'poor'];
        $builder
            ->add('language')
            ->add('reading', ChoiceType::class, [
                'choices' => $type
            ])
            ->add('writing', ChoiceType::class, [
                'choices' => $type
            ])
            ->add('speaking', ChoiceType::class, [
                'choices' => $type
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobSeekerBundle\Entity\JobSeekerLanguage'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobseekerbundle_jobseekerlanguage';
    }


}
