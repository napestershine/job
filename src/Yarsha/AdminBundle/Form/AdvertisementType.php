<?php

namespace Yarsha\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\AdminBundle\Entity\Advertisement;

class AdvertisementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        foreach(Advertisement::$advSections as $k => $v){
            $choices[$v['label'] . '('.$v['w']. ' * '. $v['h'] . ')'] = $k;
        }

        $builder->add('caption')
            ->add('file')
            ->add('status', null, [
                'label' => 'Publish'
            ])
            ->add('section', ChoiceType::class, [
                'label' => 'Advertisement Section',
                'required' => true,
                'choices' => $choices
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yarsha\AdminBundle\Entity\Advertisement'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_adminbundle_advertisement';
    }


}
