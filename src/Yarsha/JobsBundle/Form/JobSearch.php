<?php

namespace Yarsha\JobsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\JobsBundle\Entity\Job;


class JobSearch extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\Category',
                'required' => false,
                'placeholder' => '-- Select Category --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                }
            ])
            ->add('user', EntityType::class, [
                'class' => Organization::class,
                'required' => false,
                'label' => 'Organization',
                'placeholder' => '-- Select Organization --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('o')
                        ->where('o.status = 201');

                }
            ])
            ->add('from', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'From',
//                'widget' => 'single_text',
//                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-mm-dd',
                    'class' => 'from'
                ]
            ])
            ->add('to', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'To',
//                'widget' => 'single_text',
//                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'yyyy-mm-dd',
                    'class' => 'to'
                ]
            ])
            ->add('title', null, [
                'attr' => [
                    'placeholder' => 'Job Title',

                ],
                'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobsBundle\Entity\Job'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobsbundle_job';
    }


}
