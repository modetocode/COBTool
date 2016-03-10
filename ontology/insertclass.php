
<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$tip = $_GET["tip"];
if ($tip == 1) {
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);

    $name = $_GET['ime'];
    for ($i = 0; $i < strlen($name); $i++) {
        if ($name[$i] == ' ') {
            $name[$i] = '_';
        }
    }
    //echo $name;
    $class->name = $name;
    $class->description = $_GET['opis'];
    $class->superclass = $_GET['id'];
    $class->userid = $_GET['userid'];
    $class->course_modulesid = $_GET["moduleid"];
    if (is_Teacher())
        $class->status = '2';
    else
        $class->status = '1';
    $class->points = '0';
    $DB->insert_record('ontology_class', $class);
}
?>
<br /><br />
<b><?php echo get_string('Adding_new_class', 'ontology') . ':'; ?></b>
<table>
    <tr>
        <td>
<?php echo get_string('Name', 'ontology') . ':'; ?>   
        </td>
        <td>
            <input class=" ui-widget ui-state-hover" type="text" name="ime" id="ime"/> <label title="errorime" id="errorime" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_name', 'ontology') ?></label>
        </td>
    </tr>
    <tr>
        <td>
<?php echo get_string('Description', 'ontology') . ':'; ?>
        </td>
        <td>
            <input class=" ui-widget ui-state-hover" type="text" id="opis" name="opis"/> 
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td>
            <input type="button" title="ok" name="ok" value="OK" onclick="insertclass();"/>
        </td>
    </tr>
</table>