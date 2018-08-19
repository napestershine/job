<?php

namespace Yarsha\JobSeekerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Yarsha\MainBundle\Entity\Country;

class JobSeekerEducationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('degree', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\EducationDegree',
                'label' => 'Degree',
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.deleted = 0')
                        ->orWhere('e.deleted is null')
                        ->orderBy('e.sortOrder', 'ASC');
                }
            ])
            ->add('degreeName', TextType::class, [
                'label' => 'Degree Name',
                'required' => false,
                'attr' => [
                    'placeholder' => '+2, BBA, BIT, MBA, MBS'

                ]
            ])
            ->add('year', null, [
                'label' => 'Passed Year',
                'attr' => [
                    'placeholder' => 'Year'

                ]
            ])
            ->add('institution', null, [
                'label' => 'Institution (School/College)',
                'attr' => [
                    'placeholder' => 'School/College'

                ]
            ])
            ->add('board', null, [
                'label' => 'Board/University',
                'attr' => [
                    'placeholder' => 'Kathmandu University'

                ]
            ])
//            ->add('markSystem', null, [
//                'attr' => [
//                    'placeholder' => 'Percentage/Grade/CGPA'
//
//                ]
//            ])
            ->add('percentage', null, [
                'attr' => [
                    'placeholder' => '%/CGPA/Grade'

                ]
            ])
            ->add('specification', TextType::class, [
                'label' => 'Specilization',
                'attr' => [
                    'placeholder' => 'Account, Finance, Marketing'

                ]
            ])
            ->add('country', EntityType::class, [
                'choice_label' => 'name',
                'class' => Country::class,
                'query_builder' => function (EntityRepository $er) {
                    $er->findBy([
                        'deleted' => 0
                    ]);
                }
            ]);
//            ->add('delete', ButtonType::class, ['attr' => ['class' => 'delete-education']]);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobSeekerBundle\Entity\JobSeekerEducation'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobseekerbundle_jobseekereducation';
    }


}
