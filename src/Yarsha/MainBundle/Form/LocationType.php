<?php

namespace Yarsha\MainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isUpdatingLocation = $options['isUpdatingLocation'];
        $attrLabel = [];
        if (!$isUpdatingLocation) {
            $attrLabel = [
                'class' => 'hidden'
            ];

        }

        $builder->add('name', null, [
            'label_attr' => $attrLabel
        ]);
        if (!$isUpdatingLocation) {
            $builder->add('country');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\MainBundle\Entity\Location',
            'isUpdatingLocation' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_mainbundle_location';
    }


}
