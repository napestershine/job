<?php

namespace Yarsha\ArticleBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\ArticleBundle\Entity\Testimonial;

class TestimonialType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Full Name'
                ]
            ])
            ->add('company', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Company name'
                ]
            ])
            ->add('message', CKEditorType::class, [
                'attr' => ['placeholder' => 'Message']
            ])
            ->add('file')
        ;
        if($options['seeker'] == false){
            $builder
                ->add('sortOrder')
                ->add('status', ChoiceType::class, [
                    'choices' => array_flip(Testimonial::$testimonialStatusOptions),
                    'expanded' => true,
                    'multiple' => false
                ]);
        }


    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\ArticleBundle\Entity\Testimonial',
            'seeker' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_articlebundle_testimonial';
    }


}
