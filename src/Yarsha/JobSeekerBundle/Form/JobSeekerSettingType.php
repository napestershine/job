<?php

namespace Yarsha\JobSeekerBundle\Form;

use Doctrine\DBAL\Types\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobSeekerSettingType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('travelForJob', ChoiceType::class, [
                'label' =>'Are you willing to travel?',
                'expanded'=>true,
                'choices' => [
                    'Yes' => '1',
                    'No' => '0'
                ]
            ])
            ->add('haveLicense', ChoiceType::class, [
                'label' => 'Do you have driving license?',
                'choices' => [
                    'Yes' => '1',
                    'No' => '0'
                ],
                'expanded'=>true,
                'attr' => [
                    'class' => 'haveLicense',
                    'id' => 'haveLicense'
                ]
            ])
            ->add('haveLicenseOf', ChoiceType::class, [
                'label' => 'Have license of',
                'choices' => [
                    'Two Wheeler' => 'two wheeler',
                    'Four Wheeler' => 'four wheeler',
                    'Both' => 'both',
                    'None' => 'none',
                ],
                'attr' => [
                    'id' => 'haveLicenseOf',
                    'class' => 'haveLicenseOf'
                ]
            ])
            ->add('willingToRelocation', ChoiceType::class, [
                'label' => 'Are you willing to relocate?',
                'expanded'=>true,
                'choices' => [
                    'Yes' => '1',
                    'No' => '0'
                ]
            ])
            ->add('haveVehicle', ChoiceType::class, [
                'expanded'=>true,
                'choices' => [
                    'Yes' => '1',
                    'No' => '0'
                ],
                'attr' => [
                    'class' => 'haveVehicle',
                    'id' => 'haveVehicle',
                ]
            ])
            ->add('vehicleType', ChoiceType::class, [
                'label' => 'Vehicle type',
                'choices' => [
                    'Two Wheeler' => 'two wheeler',
                    'Four Wheeler' => 'four wheeler',
                    'Both' => 'both',
                    'None' => 'none',
                ],
                'attr' => [
                    'class' => 'vehicleType',
                    'id' => 'vehicleType'
                ]
            ])
            ->add('profile_searchable', ChoiceType::class, [
                'expanded'=>true,
                'choices' => [
                    'Yes' => '1',
                    'No' => '0'
                ]
            ])
//            ->add('profile_confidential')
            ->add('job_alert_table', ChoiceType::class, [
                'label' => 'Would you like to receive job alerts?',
                'expanded'=>true,
                'choices' => [
                    'Yes' => '1',
                    'No' => '0'
                ]
            ])
//            ->add('facebook_alert')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobSeekerBundle\Entity\JobSeekerSetting'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobseekerbundle_jobseekersetting';
    }


}
