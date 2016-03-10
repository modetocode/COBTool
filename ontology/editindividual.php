<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
$id = $_GET["id"];
$individual = $DB->get_record('ontology_individual', array('id' => $id));
?>
<br /> <br /> 
<h4><?php echo get_string('Edit_individual', 'ontology') . ':'; ?>&nbsp;<?php echo $individual->name; ?></h4>
<br />
<table>
    <tr>
        <td>
            <?php echo get_string('Name', 'ontology') ?>:
        </td>
        <td>
            <input type="text" class="ui-widget ui-state-hover" name="ime" value="<?php echo $individual->name; ?>" id="ime"/> <label title="errorime" id="errorime" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_name', 'ontology') ?></label>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo get_string('Description', 'ontology') ?>:
        </td>
        <td>
            <input type="text" class="ui-widget ui-state-hover" value="<?php echo $individual->description; ?>" id="opis" name="opis"/>
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td>
            <input type="button" title="ok" name="ok" value="OK" onclick="editindividual()"/>
        </td>
    </tr>
</table>

<?php
$tip = $_GET['tip'];
if ($tip == '1') {
    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once(dirname(__FILE__) . '/lib.php');
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
    $id = $_GET['id'];
    $individual = $DB->get_record('ontology_individual', array('id' => $id));
    $name = $_GET['name'];
    for ($i = 0; $i < strlen($name); $i++) {
        if ($name[$i] == ' ') {
            $name[$i] = '_';
        }
    }
    $individual->name = $name;
    $individual->description = $_GET["description"];
    if (is_Teacher())
        $individual->status = '2';
    else
        $individual->status = '1';
    $individual->points = '0';
    $individual->userid = $_GET["userid"];
    $individual->course_modulesid = $_GET["moduleid"];
    $DB->update_record('ontology_individual', $individual);
}
?>