<?php
require(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
$PAGE->set_context(null);
$PAGE->set_pagelayout('admin');
require_login();
$PAGE->set_url('/client.php');
$PAGE->set_title($SITE->shortname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_cacheable(false);
$PAGE->settingsnav->get('root')->get('playlyfe')->get('client')->make_active();
$PAGE->set_pagetype('admin-' . $PAGE->pagetype);
$PAGE->navigation->clear_cache();
 if (!has_capability('moodle/site:config', context_system::instance())) {
  print_error('accessdenied', 'admin');
}

class client_form extends moodleform {

    function definition() {

        $mform =& $this->_form;
        $mform->addElement('header','displayinfo', 'Client');
        $mform->addElement('text', 'id', 'Client ID');
        $mform->addRule('id', null, 'required', null, 'client');
        $mform->setType('id', PARAM_RAW);
        $mform->addElement('text', 'secret', 'Client Secret');
        $mform->addRule('secret', null, 'required', null, 'client');
        $mform->setType('secret', PARAM_RAW);
        $this->add_action_buttons();
    }
}

$form = new client_form();

if($form->is_cancelled()) {
  redirect(new moodle_url('/local/playlyfe/client.php'));
} else if ($data = $form->get_data()) {
  set_config('client_id', $data->id, 'playlyfe');
  set_config('client_secret', $data->secret, 'playlyfe');
  redirect(new moodle_url('/local/playlyfe/client.php'));
} else {
  $toform = array();
  $toform['id'] = get_config('playlyfe', 'client_id');
  $toform['secret'] = get_config('playlyfe', 'client_secret');
  $form->set_data($toform);
  echo $OUTPUT->header();
  $form->display();
  echo $OUTPUT->footer();
}
