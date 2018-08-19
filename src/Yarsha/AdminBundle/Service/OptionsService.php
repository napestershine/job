<?php

namespace Yarsha\AdminBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;
use Yarsha\AdminBundle\Entity\Options;
use Yarsha\AdminBundle\OptionsConstants;
use Yarsha\MainBundle\Service\AbstractService;

/**
 * Class OptionsService
 * @package Yarsha\AdminBundle\Service
 *
 * @DI\Service("yarsha.service.options", parent="yarsha.service.abstract")
 * @DI\Tag("yarsha.abstract_service")
 */
class OptionsService extends AbstractService
{

    public function getValueByName($name, $default = '')
    {
        $option = $this->getEntityManager()->getRepository(Options::class)->findOneBy(['name' => $name]);

        return ($option and $option->getValue() != "") ? $option->getValue() : $default;
    }

    public function update($data = [])
    {
        if( count($data) )
        {
            $optionsRepo = $this->getEntityManager()->getRepository(Options::class);

            foreach($data as $key => $value)
            {
                $option = $optionsRepo->findOneBy(['name' => $key]);

                if( ! $option )
                {
                    $option = new Options();
                    $option->setName($key);
                }

                $option->setValue($value);
                $this->getEntityManager()->persist($option);
            }

            try{
                $this->getEntityManager()->flush();
            }catch(\Exception $e)
            {
                throw $e;
            }
        }

        return true;
    }

    public function getOptions($group = '')
    {
        $options = $this->getEntityManager()->getRepository(Options::class)->getOptions();

        $result = $this->setSettings($options);

        return ( $group != "" and isset($result[$group]) ) ? $result[$group] : $result;
    }

    public function setSettings($options = [])
    {
        $mappings = $this->settingsMap();

        foreach($mappings as $key => $value)
        {
            foreach($value as $k => $v)
            {
                if( isset($options[$k]) )
                {
                    $mappings[$key][$k]['default'] = $options[$k]['value'];
                }
            }
        }

        return $mappings;
    }

    public function settingsMap()
    {
        $mappings[OptionsConstants::OPTION_GROUPS_SOCIAL_LINK] = OptionsConstants::$socialLinksGroup;

        return $mappings;
    }


}
