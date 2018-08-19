<?php

namespace Yarsha\JobsBundle;


class JobConstants
{

    /**
     * JOBS STATUS
     */
    const JOB_STATUS_PENDING = 200;

    const JOB_STATUS_APPROVED = 201;

    const JOB_STATUS_DISABLED = 202;

    const   JOB_STATUS_DELETED = 203;

    public static $jobStatusDesc = [
        self::JOB_STATUS_PENDING => 'Pending',
        self::JOB_STATUS_APPROVED => 'Approved',
        self::JOB_STATUS_DISABLED => 'Disabled',
    ];

    /**
     *  JOBS FROM
     */
    const JOB_FROM_EMPLOYERS = 'employers';

    const JOB_FROM_NEWSPAPER = 'newspaper';

    const JOB_FROM_GOVERNMENT = 'government';

    const JOB_FROM_ADMIN = 'admin';

    /**
     * JOBS TYPE
     */
    const JOBS_TYPE_FREE = 'free';

    const JOBS_TYPE_FEATURED = 'featured';

    const JOBS_TYPE_HOT = 'hot';

    const JOBS_TYPE_NEWSPAPER = 'newspaper';

    public static $jobsTypeDesc = [
        self::JOBS_TYPE_HOT => 'Hot',
        self::JOBS_TYPE_FEATURED => 'Featured',
        self::JOBS_TYPE_FREE => 'Free',
        self::JOBS_TYPE_NEWSPAPER => 'Newspaper'
    ];


    /**
     * JOBS AVAILABILITY TYPE
     */
    const JOBS_AVAILABILITY_FULL_TIME = 'full';

    const JOBS_AVAILABILITY_PART_TIME = 'part';

    const JOBS_AVAILABILITY_CONTRACT = 'contract';

    const JOBS_AVAILABILITY_TEMPORARY = 'temporary';

    const JOBS_AVAILABILITY_CONSULTANT = 'consultant';

    const JOBS_AVAILABILITY_OTHER = 'other';

    public static $jobsAvailabilityDesc = [
        self::JOBS_AVAILABILITY_FULL_TIME => 'Full Time',
        self::JOBS_AVAILABILITY_PART_TIME => 'Part Time',
        self::JOBS_AVAILABILITY_CONTRACT => 'Contract',
        self::JOBS_AVAILABILITY_TEMPORARY => 'Temporary',
        self::JOBS_AVAILABILITY_CONSULTANT => 'Consultant',
        self::JOBS_AVAILABILITY_OTHER => 'Other',
    ];

    const JOBS_SALARY_PAYMENT_BASIS_HOUR = 'hour';

    const JOBS_SALARY_PAYMENT_BASIS_DAY = 'day';

    const JOBS_SALARY_PAYMENT_BASIS_WEEK = 'week';

    const JOBS_SALARY_PAYMENT_BASIS_MONTH = 'month';

    const JOBS_SALARY_PAYMENT_BASIS_YEAR = 'year';

    public static $jobsPaymentBasis = [
        self::JOBS_SALARY_PAYMENT_BASIS_HOUR => 'Per Hour',
        self::JOBS_SALARY_PAYMENT_BASIS_DAY => 'Per Day',
        self::JOBS_SALARY_PAYMENT_BASIS_WEEK => 'Per Week',
        self::JOBS_SALARY_PAYMENT_BASIS_MONTH => 'Per Month',
        self::JOBS_SALARY_PAYMENT_BASIS_YEAR => 'Per Year'
    ];

    const JOB_TEMPLATE_FEATURED = 'featured';

    const JOB_TEMPLATE_NORMAL = 'normal';

    const JOB_TEMPLATE_HOT = 'hot';

    const JOB_TEMPLATE_SUPER = 'newspaper';
}
