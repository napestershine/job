<?php

namespace Yarsha\EmployerBundle;

final class YarshaEmployerEvents
{
    const CHANGE_PASSWORD_INITIALIZE = 'yarsha_employer.change_password.edit.initialize';
    const CHANGE_PASSWORD_SUCCESS = 'yarsha_employer.change_password.edit.success';
    const CHANGE_PASSWORD_COMPLETED = 'yarsha_employer.change_password.edit.completed';
    const GROUP_CREATE_INITIALIZE = 'yarsha_employer.group.create.initialize';
    const GROUP_CREATE_SUCCESS = 'yarsha_employer.group.create.success';
    const GROUP_CREATE_COMPLETED = 'yarsha_employer.group.create.completed';
    const GROUP_DELETE_COMPLETED = 'yarsha_employer.group.delete.completed';
    const GROUP_EDIT_INITIALIZE = 'yarsha_employer.group.edit.initialize';
    const GROUP_EDIT_SUCCESS = 'yarsha_employer.group.edit.success';
    const GROUP_EDIT_COMPLETED = 'yarsha_employer.group.edit.completed';
    const PROFILE_EDIT_INITIALIZE = 'yarsha_employer.profile.edit.initialize';
    const PROFILE_EDIT_SUCCESS = 'yarsha_employer.profile.edit.success';
    const PROFILE_EDIT_COMPLETED = 'yarsha_employer.profile.edit.completed';
    const REGISTRATION_INITIALIZE = 'yarsha_employer.registration.initialize';
    const REGISTRATION_SUCCESS = 'yarsha_employer.registration.success';
    const REGISTRATION_FAILURE = 'yarsha_employer.registration.failure';
    const REGISTRATION_COMPLETED = 'yarsha_employer.registration.completed';
    const REGISTRATION_CONFIRM = 'yarsha_employer.registration.confirm';
    const REGISTRATION_CONFIRMED = 'yarsha_employer.registration.confirmed';
    const RESETTING_RESET_REQUEST = 'yarsha_employer.resetting.reset.request';
    const RESETTING_RESET_INITIALIZE = 'yarsha_employer.resetting.reset.initialize';
    const RESETTING_RESET_SUCCESS = 'yarsha_employer.resetting.reset.success';
    const RESETTING_RESET_COMPLETED = 'yarsha_employer.resetting.reset.completed';
    const SECURITY_IMPLICIT_LOGIN = 'yarsha_employer.security.implicit_login';
    const RESETTING_SEND_EMAIL_INITIALIZE = 'yarsha_employer.resetting.send_email.initialize';
    const RESETTING_SEND_EMAIL_CONFIRM = 'yarsha_employer.resetting.send_email.confirm';
    const RESETTING_SEND_EMAIL_COMPLETED = 'yarsha_employer.resetting.send_email.completed';
    const USER_CREATED = 'yarsha_employer.user.created';
    const USER_PASSWORD_CHANGED = 'yarsha_employer.user.password_changed';
    const USER_ACTIVATED = 'yarsha_employer.user.activated';
    const USER_DEACTIVATED = 'yarsha_employer.user.deactivated';
    const USER_PROMOTED = 'yarsha_employer.user.promoted';
    const USER_DEMOTED = 'yarsha_employer.user.demoted';
    const USER_JOB_POST = 'yarsha_employer.job.posted';
    const EMPLOYER_JOB_POST = 'yarsha_employer.email_event.job_posted';
    const EMPLOYER_JOB_APPROVED = 'yarsha_employer.email_event.job_approved';
    const EMPLOYER_JOB_DELETED = 'yarsha_employer.email_event.job_deleted';
    const EMPLOYER_JOB_UPDATE = 'yarsha_employer.email_event.job_updated';
    const EMPLOYER_JOB_DISABLED = 'yarsha_employer.email_event.job_disabled';
}
