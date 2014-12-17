<?php
require(dirname(dirname(dirname(__FILE__))).'/config.php');
require('classes/sdk.php');
require_login();
if (!has_capability('moodle/site:config', context_system::instance())) {
  print_error('accessdenied', 'admin');
}
$courseid = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($course->id);
$PAGE->set_course($course);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url('/local/playlyfe/course.php');
$PAGE->set_title($SITE->shortname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_cacheable(false);
$PAGE->set_pagetype('admin-' . $PAGE->pagetype);
$PAGE->navigation->clear_cache();
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/playlyfe/reward.js'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot . '/local/playlyfe/course.js'));
$action = $pl->get('/design/versions/latest/actions/course_completed');
$metrics = $pl->get('/design/versions/latest/metrics', array('fields' => 'id,type,constraints'));
global $USER, $DB;
$pl = local_playlyfe_sdk::get_pl();
$html = '';


if (array_key_exists('id', $_POST)) {
  $action = patch_action($action, $metrics, $_POST, 'quiz_id');
  try {
    $pl->patch('/design/versions/latest/actions/course_completed', array(), $action);
  }
  catch(Exception $e) {
    print_object($e);
  }
  if(array_key_exists('leaderboard_metric', $_POST)) {
    $leaderboard_metric = $_POST['leaderboard_metric'];
    set_config('course'.$id, $leaderboard_metric, 'playlyfe');
    $pl->post('/admin/leaderboards/'.$leaderboard_metric.'/course'.$id, array());
  }
  redirect(new moodle_url('/local/playlyfe/course.php', array('id' => $id)));
} else {
  $course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
  $modinfo = get_fast_modinfo($course);
  $modnames = get_module_types_names();
  $modnamesused = $modinfo->get_used_module_names();
  $mods = $modinfo->get_cms();
  $sections = $modinfo->get_section_info_all();
  $name = $course->fullname;
  $rewards = array();
  foreach($action['rules'] as $rule) {
    if ($rule['requires']['context']['rhs'] == $id) {
      $rewards = $rule['rewards'];
    }
  }
  $data = array(
    'metrics' => $metrics,
    'leaderboard' => get_config('playlyfe', 'course'.$id),
    'rewards' => $rewards
  );
  echo $OUTPUT->header();
  $html .= "<h1> $name </h1>";
  $html .= '<form id="mform1" action="course.php" method="post">';
  $html .= '<input name="id" type="hidden" value="'.$id.'"/>';
  $html .= '<h2> Enable Leaderboard </h2>';
  $html .= '<div id="leaderboard">';
  $html .= '<input id="leaderboard_enable" name="leadeboard" type="checkbox" />';
  $html .= '</div>';
  $html .= "<h2> Rewards on Course Completion </h2>";
  $html .= '<table id="reward" class="admintable generaltable">';
  $html .= '<thead>';
  $html .= '<tr>';
  $html .= '<th class="header c1 lastcol centeralign" style="" scope="col">Metric</th>';
  $html .= '<th class="header c1 lastcol centeralign" style="" scope="col">Value</th>';
  $html .= '</tr>';
  $html .= '</thead>';
  $html .= '<tbody>';
  $html .= '</tbody>';
  $html .= '</table>';
  $html .= '<p><button type="button" id="add">Add Reward</button></p>';
  $html .= '<input id="submit" type="submit" name="submit" value="Submit" />';
  $html .= '</form>';
  echo $html;
  $PAGE->requires->js_init_call('setup', array($data));
  echo $OUTPUT->footer();
}
