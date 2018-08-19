<?php

namespace Yarsha\OrganizationBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class OrganizationTypeType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', CKEditorType::class)
            ->add('parent', EntityType::class, [
                'required' => false,
                'class' => 'Yarsha\OrganizationBundle\Entity\OrganizationType',
                'placeholder' => 'Choose parent',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('o')
                        ->where('o.status = :status')->setParameter('status', 1)
//                        ->andWhere('o.parent is null')
                        ->orderBy('o.name', 'ASC');
                },
                'choice_label' => 'name',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\OrganizationBundle\Entity\OrganizationType'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_organizationbundle_organizationtype';
    }


}
