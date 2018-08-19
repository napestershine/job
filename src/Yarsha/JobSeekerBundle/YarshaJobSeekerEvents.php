<?php

namespace Yarsha\JobSeekerBundle;

final class YarshaJobSeekerEvents
{
    const CHANGE_PASSWORD_INITIALIZE = 'yarsha_job_seeker.change_password.edit.initialize';
    const CHANGE_PASSWORD_SUCCESS = 'yarsha_job_seeker.change_password.edit.success';
    const CHANGE_PASSWORD_COMPLETED = 'yarsha_job_seeker.change_password.edit.completed';
    const GROUP_CREATE_INITIALIZE = 'yarsha_job_seeker.group.create.initialize';
    const GROUP_CREATE_SUCCESS = 'yarsha_job_seeker.group.create.success';
    const GROUP_CREATE_COMPLETED = 'yarsha_job_seeker.group.create.completed';
    const GROUP_DELETE_COMPLETED = 'yarsha_job_seeker.group.delete.completed';
    const GROUP_EDIT_INITIALIZE = 'yarsha_job_seeker.group.edit.initialize';
    const GROUP_EDIT_SUCCESS = 'yarsha_job_seeker.group.edit.success';
    const GROUP_EDIT_COMPLETED = 'yarsha_job_seeker.group.edit.completed';
    const PROFILE_EDIT_INITIALIZE = 'yarsha_job_seeker.profile.edit.initialize';
    const PROFILE_EDIT_SUCCESS = 'yarsha_job_seeker.profile.edit.success';
    const PROFILE_EDIT_COMPLETED = 'yarsha_job_seeker.profile.edit.completed';
    const REGISTRATION_INITIALIZE = 'yarsha_job_seeker.registration.initialize';
    const REGISTRATION_SUCCESS = 'yarsha_job_seeker.registration.success';
    const REGISTRATION_FAILURE = 'yarsha_job_seeker.registration.failure';
    const REGISTRATION_COMPLETED = 'yarsha_job_seeker.registration.completed';
    const REGISTRATION_CONFIRM = 'yarsha_job_seeker.registration.confirm';
    const REGISTRATION_CONFIRMED = 'yarsha_job_seeker.registration.confirmed';
    const RESETTING_RESET_REQUEST = 'yarsha_job_seeker.resetting.reset.request';
    const RESETTING_RESET_INITIALIZE = 'yarsha_job_seeker.resetting.reset.initialize';
    const RESETTING_RESET_SUCCESS = 'yarsha_job_seeker.resetting.reset.success';
    const RESETTING_RESET_COMPLETED = 'yarsha_job_seeker.resetting.reset.completed';
    const SECURITY_IMPLICIT_LOGIN = 'yarsha_job_seeker.security.implicit_login';
    const RESETTING_SEND_EMAIL_INITIALIZE = 'yarsha_job_seeker.resetting.send_email.initialize';
    const RESETTING_SEND_EMAIL_CONFIRM = 'yarsha_job_seeker.resetting.send_email.confirm';
    const RESETTING_SEND_EMAIL_COMPLETED = 'yarsha_job_seeker.resetting.send_email.completed';
    const USER_CREATED = 'yarsha_job_seeker.user.created';
    const USER_PASSWORD_CHANGED = 'yarsha_job_seeker.user.password_changed';
    const USER_ACTIVATED = 'yarsha_job_seeker.user.activated';
    const USER_DEACTIVATED = 'yarsha_job_seeker.user.deactivated';
    const USER_PROMOTED = 'yarsha_job_seeker.user.promoted';
    const USER_DEMOTED = 'yarsha_job_seeker.user.demoted';
}
