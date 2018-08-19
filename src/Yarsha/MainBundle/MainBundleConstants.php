<?php

namespace Yarsha\MainBundle;


class MainBundleConstants
{

    /**
     * USER TYPE
     */
    const USER_TYPE_ADMIN = 1;

    const USER_TYPE_EMPLOYER = 2;

    const USER_TYPE_EMPLOYEE = 3;

    /**
     * GENDER
     */
    const GENDER_MALE = 'male';

    const GENDER_FEMALE = 'female';

    const GENDER_OTHERS = 'others';

    public static $genderDesc = [
        self::GENDER_MALE => 'Male',
        self::GENDER_FEMALE => 'Female',
        self::GENDER_OTHERS => 'Any'
    ];


    /**
     * Marital Status
     */
    const SINGLE = 'single';

    const MARRIED = 'married';

    const UNMARRIED = 'unmarried';

    public static $maritalStatus = [
        self::SINGLE => 'Single',
        self::MARRIED => 'Married',
        self::UNMARRIED => 'Unmarried'
    ];


    /**
     * Experience Year
     */

    const ONE = '1-2';

    const TWO = '2-4';

    const THREE = '4-6';

    const FOUR = '6-8';

    const FIVE = '8-10';

    const ABOVE = '10-100';


    public static $experienceYear = [
        self::ONE => '1-2',
        self::TWO => '2-4',
        self::THREE => '4-6',
        self::FOUR => '6-8',
        self::FIVE => '8-10',
        self::ABOVE => '10 above'
    ];

    const ENTRY_LEVEL = 'entry';

    const MID_LEVEL = 'mid';

    const SENIOR_LEVEL = 'senior';

    public static $preferredPositions = [
        self::ENTRY_LEVEL => 'Entry Level',
        self::MID_LEVEL => 'Mid Level',
        self::SENIOR_LEVEL => 'Senoir Level'
    ];

    const SEEKER_EMAIL_FOR_BIRTHDAY_MESSAGE = 'birthday_message';
    const SEEKER_EMAIL_FOR_PROFILE_UPDATE_ALERT = 'profile_update_alert';
    const SEEKER_EMAIL_FOR_JOB_ALERT = 'job_alert';


}
