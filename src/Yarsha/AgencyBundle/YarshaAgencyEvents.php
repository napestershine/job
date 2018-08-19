<?php

namespace Yarsha\AgencyBundle;

final class YarshaAgencyEvents
{
    const CHANGE_PASSWORD_INITIALIZE = 'yarsha_agency.change_password.edit.initialize';
    const CHANGE_PASSWORD_SUCCESS = 'yarsha_agency.change_password.edit.success';
    const CHANGE_PASSWORD_COMPLETED = 'yarsha_agency.change_password.edit.completed';
    const GROUP_CREATE_INITIALIZE = 'yarsha_agency.group.create.initialize';
    const GROUP_CREATE_SUCCESS = 'yarsha_agency.group.create.success';
    const GROUP_CREATE_COMPLETED = 'yarsha_agency.group.create.completed';
    const GROUP_DELETE_COMPLETED = 'yarsha_agency.group.delete.completed';
    const GROUP_EDIT_INITIALIZE = 'yarsha_agency.group.edit.initialize';
    const GROUP_EDIT_SUCCESS = 'yarsha_agency.group.edit.success';
    const GROUP_EDIT_COMPLETED = 'yarsha_agency.group.edit.completed';
    const PROFILE_EDIT_INITIALIZE = 'yarsha_agency.profile.edit.initialize';
    const PROFILE_EDIT_SUCCESS = 'yarsha_agency.profile.edit.success';
    const PROFILE_EDIT_COMPLETED = 'yarsha_agency.profile.edit.completed';
    const REGISTRATION_INITIALIZE = 'yarsha_agency.registration.initialize';
    const REGISTRATION_SUCCESS = 'yarsha_agency.registration.success';
    const REGISTRATION_FAILURE = 'yarsha_agency.registration.failure';
    const REGISTRATION_COMPLETED = 'yarsha_agency.registration.completed';
    const REGISTRATION_CONFIRM = 'yarsha_agency.registration.confirm';
    const REGISTRATION_CONFIRMED = 'yarsha_agency.registration.confirmed';
    const RESETTING_RESET_REQUEST = 'yarsha_agency.resetting.reset.request';
    const RESETTING_RESET_INITIALIZE = 'yarsha_agency.resetting.reset.initialize';
    const RESETTING_RESET_SUCCESS = 'yarsha_agency.resetting.reset.success';
    const RESETTING_RESET_COMPLETED = 'yarsha_agency.resetting.reset.completed';
    const SECURITY_IMPLICIT_LOGIN = 'yarsha_agency.security.implicit_login';
    const RESETTING_SEND_EMAIL_INITIALIZE = 'yarsha_agency.resetting.send_email.initialize';
    const RESETTING_SEND_EMAIL_CONFIRM = 'yarsha_agency.resetting.send_email.confirm';
    const RESETTING_SEND_EMAIL_COMPLETED = 'yarsha_agency.resetting.send_email.completed';
    const USER_CREATED = 'yarsha_agency.user.created';
    const USER_PASSWORD_CHANGED = 'yarsha_agency.user.password_changed';
    const USER_ACTIVATED = 'yarsha_agency.user.activated';
    const USER_DEACTIVATED = 'yarsha_agency.user.deactivated';
    const USER_PROMOTED = 'yarsha_agency.user.promoted';
    const USER_DEMOTED = 'yarsha_agency.user.demoted';
}
