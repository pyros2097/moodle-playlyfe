<?php
require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir . '/formslib.php');
require(dirname(dirname(__FILE__)).'/classes/sdk.php');
$PAGE->set_context(null);
$PAGE->set_pagelayout('admin');
require_login();
$PAGE->set_url('/local/playlyfe/metric/manage.php');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_cacheable(false);
$PAGE->settingsnav->get('root')->get('playlyfe')->get('metrics')->get('manage')->make_active();
$PAGE->navigation->clear_cache();
$html = '';

$delete = optional_param('delete', null, PARAM_TEXT);
$id = optional_param('id', null, PARAM_TEXT);
if($id and $delete) {
  $pl->delete('/design/versions/latest/metrics/'.$id, array());
  $pl->delete('/design/versions/latest/leaderboards/'.$id, array());
}

$table = new html_table();
$table->head  = array('Image', 'ID', 'Name', 'Description', '', '');
$table->colclasses = array('leftalign', 'centeralign', 'rightalign', 'rightalign', 'rightalign');
$table->data  = array();
$table->attributes['class'] = 'pl-table admin-table';
$table->id = 'manage_metrics';

$metrics = $pl->get('/design/versions/latest/metrics', array('fields' => 'id,name,type,description,image'));
foreach($metrics as $metric) {
  if($metric['type'] == 'point') {
    $edit = '<a href="edit.php?id='.$metric['id'].'">Edit</a>';
    $delete = '<a href="manage.php?id='.$metric['id'].'&delete=true'.'">Delete</a>';
    $item_image = '<img src="../image.php?image_id='.$metric['image'].'"></img>';
    $table->data[] = new html_table_row(array($item_image, $metric['id'], $metric['name'], $metric['description'], $edit, $delete));
  }
}
$html .= html_writer::table($table);
echo $OUTPUT->header();
echo '<b>Metrics</b><hr></hr>';
echo $html;
echo $OUTPUT->footer();
