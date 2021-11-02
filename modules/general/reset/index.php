<?php

if (cfr('RESET')) {
    if (ubRouting::checkGet('username')) {
        $login = ubRouting::get('username');
        // reset user if need
        $billing->resetuser($login);
        log_register('RESET User (' . $login . ')');
        //resurrect if user is disconnected
        if ($ubillingConfig->getAlterParam('RESETHARD')) {
            zb_UserResurrect($login);
        }
        ubRouting::nav(UserProfile::URL_PROFILE . $login);
    }
} else {
    show_error(__('You cant control this module'));
}

