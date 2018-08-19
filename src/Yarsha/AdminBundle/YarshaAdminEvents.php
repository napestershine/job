<?php

namespace Yarsha\AdminBundle;

final class YarshaAdminEvents
{

    const CHANGE_PASSWORD_INITIALIZE = 'yarsha_admin.change_password.edit.initialize';

    const CHANGE_PASSWORD_SUCCESS = 'yarsha_admin.change_password.edit.success';

    const CHANGE_PASSWORD_COMPLETED = 'yarsha_admin.change_password.edit.completed';

    const GROUP_CREATE_INITIALIZE = 'yarsha_admin.group.create.initialize';

    const GROUP_CREATE_SUCCESS = 'yarsha_admin.group.create.success';

    const GROUP_CREATE_COMPLETED = 'yarsha_admin.group.create.completed';

    const GROUP_DELETE_COMPLETED = 'yarsha_admin.group.delete.completed';

    const GROUP_EDIT_INITIALIZE = 'yarsha_admin.group.edit.initialize';

    const GROUP_EDIT_SUCCESS = 'yarsha_admin.group.edit.success';

    const GROUP_EDIT_COMPLETED = 'yarsha_admin.group.edit.completed';

    const PROFILE_EDIT_INITIALIZE = 'yarsha_admin.profile.edit.initialize';

    const PROFILE_EDIT_SUCCESS = 'yarsha_admin.profile.edit.success';

    const PROFILE_EDIT_COMPLETED = 'yarsha_admin.profile.edit.completed';

    const REGISTRATION_INITIALIZE = 'yarsha_admin.registration.initialize';

    const REGISTRATION_SUCCESS = 'yarsha_admin.registration.success';

    const REGISTRATION_FAILURE = 'yarsha_admin.registration.failure';

    const REGISTRATION_COMPLETED = 'yarsha_admin.registration.completed';

    const REGISTRATION_CONFIRM = 'yarsha_admin.registration.confirm';

    const REGISTRATION_CONFIRMED = 'yarsha_admin.registration.confirmed';

    const RESETTING_RESET_REQUEST = 'yarsha_admin.resetting.reset.request';

    const RESETTING_RESET_INITIALIZE = 'yarsha_admin.resetting.reset.initialize';

    const RESETTING_RESET_SUCCESS = 'yarsha_admin.resetting.reset.success';

    const RESETTING_RESET_COMPLETED = 'yarsha_admin.resetting.reset.completed';

    const SECURITY_IMPLICIT_LOGIN = 'yarsha_admin.security.implicit_login';

    const RESETTING_SEND_EMAIL_INITIALIZE = 'yarsha_admin.resetting.send_email.initialize';

    const RESETTING_SEND_EMAIL_CONFIRM = 'yarsha_admin.resetting.send_email.confirm';

    const RESETTING_SEND_EMAIL_COMPLETED = 'yarsha_admin.resetting.send_email.completed';

    const USER_CREATED = 'yarsha_admin.user.created';

    const USER_PASSWORD_CHANGED = 'yarsha_admin.user.password_changed';

    const USER_ACTIVATED = 'yarsha_admin.user.activated';

    const USER_DEACTIVATED = 'yarsha_admin.user.deactivated';

    const USER_PROMOTED = 'yarsha_admin.user.promoted';

    const USER_DEMOTED = 'yarsha_admin.user.demoted';
}
