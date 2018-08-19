<?php

namespace Yarsha\AdminBundle;

/**
 * Class OptionsConstants
 * @package Yarsha\AdminBundle
 *
 *
 */
class OptionsConstants
{

    /**
     * SOCIAL LINKS 
     */
    const OPTION_GROUPS_SOCIAL_LINK = 'social_links';

    const SOCIAL_LINK_FACEBOOK = 'social_link_facebook';
    const SOCIAL_LINK_LINKEDIN = 'social_link_linkedin';
    const SOCIAL_LINK_GOOGLE_PLUS = 'social_link_google_plus';
    const SOCIAL_LINK_YOUTUBE = 'social_link_youtube';
    const SOCIAL_LINK_TWITTER = 'social_link_twitter';

    public static $socialLinksGroup = [
        self::SOCIAL_LINK_FACEBOOK      => [ 'label' => 'Facebook', 'default' => '' ],
        self::SOCIAL_LINK_LINKEDIN      => [ 'label' => 'LinkedIn', 'default' => '' ],
        self::SOCIAL_LINK_GOOGLE_PLUS   => [ 'label' => 'Google Plus', 'default' => '' ],
        self::SOCIAL_LINK_YOUTUBE       => [ 'label' => 'Youtube Channel', 'default' => '' ],
        self::SOCIAL_LINK_TWITTER       => [ 'label' => 'Twitter', 'default' => '' ]
    ];


}