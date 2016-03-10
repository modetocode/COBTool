<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
?>

<br /> <br /> <b><?php echo get_string('Adding_new_dproperty', 'ontology') . ':'; ?></b>
<table>
    <tr>
        <td>
            <?php echo get_string('Name', 'ontology') ?>:
        </td>
        <td>
            <input class="ui-widget ui-state-hover" type="text" id="ime" name="ime"/> <label title="errorime" id="errorime" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_name', 'ontology') ?></label>
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
            <?php echo get_string('Property_range', 'ontology') ?>:
        </td>
        <td>
            <select id="rang" name="rang">
                <option value="boolean" id="boolean" title="boolean">boolean</option>
                <option value="byte" id="byte" title="byte">byte</option>
                <option value="dateTime" id="dateTime" title="dateTime">dateTime</option>
                <option value="decimal" id="decimal" title="decimal">decimal</option>
                <option value="double" id="double" title="double">double</option>
                <option value="float" id="float" title="float">float</option>
                <option value="integer" id="integer" title="integer">integer</option>
                <option value="long" id="long" title="long">long</option>
                <option value="real" id="real" title="real">real</option>
                <option value="short" id="short" title="short">short</option>
                <option value="string" id="string" title="string">string</option>
                <option value="unsignedByte" id="unsignedByte" title="unsignedByte">unsignedByte</option>
                <option value="unsignedInt" id="unsignedInt" title="unsignedInt">unsignedInt</option>
                <option value="unsignedLong" id="unsignedLong" title="unsignedLong">unsignedLong</option>
                <option value="unsignedShort" id="unsignedShort" title="unsignedShort">unsignedShort</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo get_string('Characteristics', 'ontology') . ':' ?>
        </td>
        <td>
            <input type="checkbox" title="funkcionalna" name="funkcionalna" id="funkcionalna"/> <?php echo get_string('Functional', 'ontology') ?>
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td>
            <input type="button" title="ok" name="ok" value="OK" onclick="insertdproperty()"/>
        </td>
    </tr>
</table>

<?php
$tip = $_GET["tip"];
if ($tip == '1') {
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
    global $DB;
    $property->superproperty = $_GET["superproperty"];
    $name = $_GET['name'];
    for ($i = 0; $i < strlen($name); $i++) {
        if ($name[$i] == ' ') {
            $name[$i] = '_';
        }
    }
    $property->name = $name;
    $property->description = $_GET["description"];
    //$property->range='range';
    $property->rang = $_GET["range"];
    $property->attributes = $_GET["attributes"];
    if (is_Teacher())
        $property->status = '2';
    else
        $property->status = '1';
    $property->points = '0';
    $property->userid = $_GET["userid"];
    $property->course_modulesid = $_GET["moduleid"];
    $DB->insert_record('ontology_property_data', $property);
}
else
if ($tip == '2') {//ekvivalentni klasi
    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once(dirname(__FILE__) . '/lib.php');
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
    global $DB;
    $equproperty->ontology_propertyid = $_GET["propertyid"];
    $equproperty->ontology_propertyid2 = $_GET["propertyid2"];
    $equproperty->type = '2';
    if (is_Teacher())
        $equproperty->status = '2';
    else
        $equproperty->status = '1';
    $equproperty->points = '0';
    $equproperty->userid = $_GET["userid"];
    $equproperty->course_modulesid = $_GET["moduleid"];
    $DB->insert_record('ontology_property_equivalent', $equproperty);
}
else {//disjunktni klasi
    if ($tip == '3') {
        require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
        require_once(dirname(__FILE__) . '/lib.php');
        global $DB;
        $id = $_GET["moduleid"];
        $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
        $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
        require_login($course, true, $cm);
        add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
        global $DB;
        $disproperty->ontology_propertyid = $_GET["propertyid"];
        $disproperty->ontology_propertyid2 = $_GET["propertyid2"];
        $disproperty->type = '2';
        if (is_Teacher())
            $disproperty->status = '2';
        else
            $disproperty->status = '1';
        $disproperty->points = '0';
        $disproperty->userid = $_GET["userid"];
        $disproperty->course_modulesid = $_GET["moduleid"];
        $DB->insert_record('ontology_property_disjoint', $disproperty);
    }
}
?>