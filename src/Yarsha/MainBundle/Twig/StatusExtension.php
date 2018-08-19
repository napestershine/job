<?php

namespace Yarsha\MainBundle\Twig;

//use EducationSansar\Bundle\MainBundle\Common\ESConstant;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class StatusExtension
 * @package Yarsha\MainBundle\Twig
 *
 * @DI\Service("yarsha.service.status_twig_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class StatusExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                    'ys_status',
                    [$this, 'status'],
                    ['is_safe' => ['html']]
                ),
            new \Twig_SimpleFunction(
                'ys_status_bool',
                [$this, 'booleanStatus'],
                ['is_safe' => ['html']]
            )
            ,
            new \Twig_SimpleFunction(
                'ys_user_status',
                [$this, 'userStatus'],
                ['is_safe' => ['html']]
            )
        ];
    }

    public function status($status)
    {
        $statusDesc = []; //ESConstant::$statusDescriptions;

        $label = 'unknown';
        $class = 'bg-gray';

        if( isset($statusDesc[$status]) )
        {
            $label = strtolower($statusDesc[$status]['label']);
            $class = strtolower($statusDesc[$status]['class']);
        }

        return "<span class=\"label {$class}\">{$label}</span>";
    }

    public function booleanStatus($status)
    {
        $label = 'Published';
        $class = 'label-success';

        if( $status == false )
        {
            $label = 'Unpublished';
            $class = 'label-danger';
        }

        return "<span class=\"label {$class}\">{$label}</span>";
    }

    public function userStatus($status = false)
    {
        $label = ($status == true)? 'Verified' : 'Not Verified';
        $class = ($status == true)? 'label-success' : 'label-danger';
        return "<span class=\"label {$class}\">{$label}</span>";
    }

    public function getName()
    {
        return 'status_twig_extension';
    }

}
