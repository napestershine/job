<?php

namespace Yarsha\OrganizationBundle;


class OrganizationConstants
{

    const ORGANIZATION_PROFILE_STATUS_ACTIVE = 100;

    const ORGANIZATION_PROFILE_STATUS_INACTIVE = 101;

    const ORGANIZATION_PROFILE_STATUS_HIDDEN = 102;

    const ORGANIZATION_STATUS_PENDING = 200;

    const ORGANIZATION_STATUS_APPROVED = 201;

    const ORGANIZATION_STATUS_DISABLED = 202;

    const ORGANIZATION_LABEL_EMPLOYER = 300;

    const ORGANIZATION_LABEL_TOP_EMPLOYER = 301;

    public static $organizationStatus = [
        self::ORGANIZATION_STATUS_PENDING => 'Pending',
        self::ORGANIZATION_STATUS_APPROVED => 'Approve',
        self::ORGANIZATION_STATUS_DISABLED => 'Disable'
    ];

    /**
     * Organization types
     */
    const ORGANIZATION_TYPE_EMPLOYER = 'employer';
    const ORGANIZATION_TYPE_NEWSPAPER = 'newspaper';

}
