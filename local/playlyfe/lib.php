<?php
  require_once('classes/sdk.php');

  $event_names = array(
    'course_completed',
    'user_enrolled',
    'user_logout',
    'assessable_submitted',
    'quiz_attempt_submitted'
  );

  function check_event($event, $event_name) {
    global $USER;
    print_object($event);
    if(in_array($event_name, $event_names)) {
      $data = array('player_id' => 'u'.$USER->id);
      try {
        $pl = local_playlyfe_sdk::get_pl();
        $pl->post('/runtime/actions/'.$event_name.'/play', $data, (object)array());
      }
      catch(Exception $e) {
        print_object($e);
      }
    }
  }

  function course_completed_handler($event) {
    check_event($event, 'course_completed');
  }

  function user_logout_handler($event) {
    check_event($event, 'user_logout');
  }

  function user_enrolled_handler($event) {
    print_object($event);
    check_event($event, 'user_enrolled');
  }


  function user_created_handler($event) {
    $pl = local_playlyfe_sdk::get_pl();
    if (true) {  //for moodle 2.5
      $data = array('id' => 'u'.$event->id, 'alias' => $event->username, 'email' => $event->email);
    }
    else {
      $data = $event->get_data();
      $user_id = $data['objectid'];
      $data = array('alias' => 'Anon', 'id' => 'u'.$user_id);
    }
    $pl->post('/admin/players', array(), $data);
  }

  function local_playlyfe_extends_settings_navigation(settings_navigation $settingsnav, $context) {
      $sett = $settingsnav->get('root');
      if($sett != null) {
        $nodePlaylyfe = $sett->add('Gamification', null, null, null, 'playlyfe');

        $nodePlaylyfe->add('Client', new moodle_url('/local/playlyfe/client.php'), null, null, 'client', new pix_icon('t/edit', 'edit'));
        $nodePlaylyfe->add('Publish', new moodle_url('/local/playlyfe/publish.php'), null, null, 'publish', new pix_icon('t/edit', 'edit'));

        $nodePlaylyfe->add('Courses', new moodle_url('/local/playlyfe/course.php'), null, null, 'courses', new pix_icon('t/edit', 'edit'));

        $nodeMetric = $nodePlaylyfe->add('Metrics', null, null, null, 'metrics');
        $nodeMetric->add('Manage Metrics', new moodle_url('/local/playlyfe/metric/manage.php'), null, null, 'manage', new pix_icon('t/edit', 'edit'));
        $nodeMetric->add('Add a new metric', new moodle_url('/local/playlyfe/metric/add.php'), null, null, 'add', new pix_icon('t/edit', 'edit'));

        $nodeSet = $nodePlaylyfe->add('Set Badges', null, null, null, 'sets');
        $nodeSet->add('Manage sets', new moodle_url('/local/playlyfe/set/manage.php'), null, null, 'manage', new pix_icon('t/edit', 'edit'));
        $nodeSet->add('Add a new set', new moodle_url('/local/playlyfe/set/add.php'), null, null, 'add', new pix_icon('t/edit', 'edit'));
      }
  }

  function local_playlyfe_extends_navigation($navigation) {
    $nodeProfile = $navigation->add('Playlyfe Profile', new moodle_url('/local/playlyfe/profile.php'));
  }
