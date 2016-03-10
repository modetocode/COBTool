<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
?>
<br /> <br />
<b><?php echo get_string('Adding_new_individual', 'ontology') . ':'; ?></b>
<?php
$tip = $_GET["tip"];
if ($tip == '1') {  //zapisuvanje na fiksnite podatoci za individuata
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
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
    global $DB;
    $DB->insert_record('ontology_individual', $individual);
}
else
if ($tip == '2') { //zapisuvanje na podatocite za objektnoto svojstvo
    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once(dirname(__FILE__) . '/lib.php');
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
    $property->ontology_individualid = $_GET["individualid"];
    $property->ontology_individualid2 = $_GET["individualid2"];
    $property->ontology_propertyid = $_GET["propertyid"];
    if (is_Teacher())
        $property->status = '2';
    else
        $property->status = '1';
    $property->points = '0';
    $property->userid = $_GET["userid"];
    $property->course_modulesid = $_GET["moduleid"];
    global $DB;
    $DB->insert_record('ontology_individual_property_individual', $property);
}
else
if ($tip == '3') {//zapishuvanje na podatocite za podatocnoto svojstvo
    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once(dirname(__FILE__) . '/lib.php');
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
    $property->ontology_individualid = $_GET["individualid"];
    $property->ontology_propertyid = $_GET["propertyid"];
    $property->data = $_GET["data"];
    if (is_Teacher())
        $property->status = '2';
    else
        $property->status = '1';
    $property->points = '0';
    $property->userid = $_GET["userid"];
    $property->course_modulesid = $_GET["moduleid"];
    global $DB;
    $DB->insert_record('ontology_individual_property_data', $property);
    individual_hierarhy("individuals", "individual", 0, $id, $USER->id, 0);
}
?>  
<table>
    <tr>
        <td>
<?php echo get_string('Name', 'ontology') ?>:
        </td>
        <td>
            <input class=" ui-widget ui-state-hover" type="text" name="ime" id="ime"/> <label title="errorime" id="errorime" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_name', 'ontology') ?></label>
        </td>
    </tr>
    <tr>
        <td>
<?php echo get_string('Description', 'ontology') ?>:
        </td>
        <td>
            <input class=" ui-widget ui-state-hover" type="text" id="opis" name="opis"/>
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td>
            <input type="button" title="ok" name="ok" value="OK" onclick="insertindividual()"/>
        </td>
    </tr>
</table>

