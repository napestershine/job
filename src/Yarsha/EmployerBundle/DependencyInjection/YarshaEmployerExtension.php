<?php

/*
 * This file is part of the RollerworksMultiUserBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yarsha\EmployerBundle\DependencyInjection;

use Rollerworks\Bundle\MultiUserBundle\DependencyInjection\Factory\UserServicesFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


/*
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class YarshaEmployerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $factory = new UserServicesFactory($container);

        // The first parameter is the name of the user-system, the second is the configuration which internally
        // is normalized by the Symfony Config component.

        /*
            The `UserServicesFactory::create()` will also register the form-types you need,
            any form-type/name/class that belongs to the FOSUserBundle will be converted to an ready
            to use form-type. Remember form types and names start with the service-prefix of the user-system.

            ```
            'form' => array(
                'type' => 'fos_user_profile',
                'class' => 'FOS\UserBundle\Form\Type\ProfileFormType',
                'name' => 'fos_user_profile_form',
            ),
            ```

            Will internally get converted to an 'yarsha_employer_profile' form-type service definition.
            Never set the template namespace to FOSUserBundle or RollerworksMultiUserBundle as this will create an endless recursion!
        */

        $factory->create('yarsha_employer', array(
            array(
                /*
                 * `path` and `host` are used by the `RequestListener` service for finding the correct user-type.
                 * You can either set only a path or host, or both. Or you can choose to use your own matcher-service
                 * by setting the `request_matcher`, and giving it the service-id.
                 *
                 * A request-matcher must always implement the `Symfony\Component\HttpFoundation\RequestMatcherInterface`.
                 */

                'path' => '^/employer', // path-regex, must match the firewall pattern
                'host' => null,
                'request_matcher' => null,

                // When not set this will inherit from the user-system name provided above
                'services_prefix' => 'yarsha_employer',
                'routes_prefix' => 'yarsha_employer',

                'db_driver' => 'orm', // can be either: orm, mongodb, couchdb or custom (Propel is not supported)
                'model_manager_name' => 'default',
                'use_listener' => true,

                'user_class' => 'Yarsha\EmployerBundle\Entity\User',
                'firewall_name' => 'employer', // this must equal to the firewall-name used for this user-system

                'use_username_form_type' => true,

                // When not set these will inherit from the system wide configuration
                'from_email' => array(
                    'address' => null,
                    'sender_name' => null,
                ),

                'security' => array(
                    'login' => array(
                        'template' => 'YarshaEmployerBundle:UserBundle/Security:login.html.twig',
                    ),
                ),

                'service' => array(
                    'mailer' => 'fos_user.mailer.default',
                    'canonical_fields_updater' => 'fos_user.util.canonical_fields_updater',
                    'user_manager' => 'fos_user.user_manager.default',
                ),

                'template' => array(
                    'layout' => 'RollerworksMultiUserBundle::layout.html.twig',
                ),

                'profile' => array(
                    'form' => array(
                        'class' => 'FOS\\UserBundle\\Form\\Type\\ProfileFormType',
                        'type' => 'fos_user_profile',
                        'name' => 'fos_user_profile_form',
                        'validation_groups' => array('Profile', 'Default'),
                    ),
                    'template' => array(
                        'edit' => 'RollerworksMultiUserBundle:UserBundle/Profile:edit.html.twig',
                        'show' => 'RollerworksMultiUserBundle:UserBundle/Profile:show.html.twig',
                    ),
                ),

                'change_password' => array(
                    'form' => array(
                        'class' => 'FOS\\UserBundle\\Form\\Type\\ChangePasswordFormType',
                        'type' => 'fos_user_change_password',
                        'name' => 'fos_user_change_password_form',
                        'validation_groups' => array('ChangePassword', 'Default'),
                    ),
                    'template' => array(
                        'change_password' => 'RollerworksMultiUserBundle:UserBundle/ChangePassword:changePassword.html.twig',
                    ),
                ),

                'registration' => array(
                    'confirmation' => array(
                        'enabled' => true,
                        'template' => array(
                            'email' => 'RollerworksMultiUserBundle:UserBundle/Registration:email.txt.twig',
                            'confirmed' => 'RollerworksMultiUserBundle:UserBundle/Registration:confirmed.html.twig',
                        ),
                        'from_email' => array(
                            'address' => null,
                            'sender_name' => null,
                        ),
                    ),
                    'form' => array(
                        'class' => 'FOS\\UserBundle\\Form\\Type\\RegistrationFormType',
                        'type' => 'fos_user_registration',
                        'name' => 'fos_user_registration_form',
                        'validation_groups' => array('Registration', 'Default'),
                    ),
                    'template' => array(
                        'register' => 'RollerworksMultiUserBundle:UserBundle/Registration:register.html.twig',
                        'check_email' => 'RollerworksMultiUserBundle:UserBundle/Registration:checkEmail.html.twig',
                    ),
                ),

                'resetting' => array(
                    'token_ttl' => 86400,
                    'email' => array(
                        'from_email' => array(
                            'address' => null,
                            'sender_name' => null,
                        ),
                    ),
                    'form' => array(
                        'template' => null,
                        'class' => 'FOS\\UserBundle\\Form\\Type\\ResettingFormType',
                        'type' => 'fos_user_resetting',
                        'name' => 'fos_user_resetting_form',
                        'validation_groups' => array('ResetPassword', 'Default'),
                    ),
                    'template' => array(
                        'check_email' => 'RollerworksMultiUserBundle:UserBundle/Resetting:checkEmail.html.twig',
                        'email' => 'RollerworksMultiUserBundle:UserBundle/Resetting:email.txt.twig',
                        'password_already_requested' => 'RollerworksMultiUserBundle:UserBundle/Resetting:passwordAlreadyRequested.html.twig',
                        'request' => 'RollerworksMultiUserBundle:UserBundle/Resetting:request.html.twig',
                        'reset' => 'RollerworksMultiUserBundle:UserBundle/Resetting:reset.html.twig',
                    ),
                ),

                // Optional
                /*
                'group' => array(
                    'group_class' => 'Yarsha\EmployerBundle\Entity\Group',
                    'group_manager' => 'fos_user.group_manager.default',

                    'form' => array(
                        'class' => 'FOS\\UserBundle\\Form\\Type\\GroupFormType',
                        'type' => 'fos_user_group',
                        'name' => 'fos_user_group_form',
                        'validation_groups' => array('Registration', 'Default'),
                    ),
                    'template' => array(
                        'edit' => 'RollerworksMultiUserBundle:UserBundle/Group:edit.html.twig',
                        'list' => 'RollerworksMultiUserBundle:UserBundle/Group:list.html.twig',
                        'new' => 'RollerworksMultiUserBundle:UserBundle/Group:new.html.twig',
                        'show' => 'RollerworksMultiUserBundle:UserBundle/Group:show.html.twig',
                    ),
                ),
                */
            ),
            $config,
        ));

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');}
}
