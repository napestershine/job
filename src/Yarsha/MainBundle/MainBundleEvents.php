<?php

namespace Yarsha\MainBundle;


final class MainBundleEvents
{

    const ADD_EDIT_POST = 'yarsha.event.add_edit_post';

    const EVENT_UPDATE_TAG = 'yarsha.event.update_tag';

    const EVENT_DELETE_TAG = 'yarsha.event.delete_tag';

    const EVENT_JOB_SEEKER_PROFILE_UPDATE = 'yarsha.event.seeker_profile_update';

    const EVENT_EMPLOYER_PROFILE_UPDATE = 'yarsha.event.employer_profile_update';

    const EMAIL_EVENT_JOB_SEEKER_JOB_APPLIED = 'yarsha_job_seeker.email_event.job_applied';

    const EMAIL_EVENT_JOB_SEEKER_APPLICATION_SHORTLISTED = 'yarsha_job_seeker.email_event.application_shortlisted';

    const EMAIL_EVENT_JOB_SEEKER_APPLICATION_REJECTED = 'yarsha_job_seeker.email_event.application_rejected';

    const EMAIL_EVENT_JOB_SEEKER_APPLICATION_SELECTED = 'yarsha_job_seeker.email_event.application_selected';
}
