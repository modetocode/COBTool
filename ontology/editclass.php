<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
$id = $_GET['id'];
$cmid = $_GET['cmid'];
$userid = $_GET['userid'];
$c = $DB->get_record('ontology_class', array('id' => $id));
?>
<br /> <br />
<h4><?php echo get_string('Edit_class', 'ontology') . ':'; ?>  
    <?php echo $c->name . "</h4>"; ?>
    <br />
    <table>
        <tr>
            <td>
                <?php
                echo get_string('Choose_new_superclass', 'ontology') . ':<br />';
                class_hierarhy4("superklasa", "superklasa1", 0, $cmid, $userid, $c->id);
                ?> <br/></td>
            <td>
                <table>
                    <tr>
                        <td>
                            <?php echo get_string('Name', 'ontology') . ':'; ?>                      
                        </td>
                        <td>
                            <input type="text" class="ui-widget ui-state-hover" name="ime" value="<?php echo $c->name; ?>" id="ime"/> <label title="errorime" id="errorime" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_name', 'ontology') ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php echo get_string('Description', 'ontology') . ':'; ?>
                        </td>
                        <td>
                            <input type="text" class="ui-widget ui-state-hover" id="opis" value="<?php echo $c->description; ?>" name="opis"/> 
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
            </td>
            <td>
                <input type="button" title="ok" name="ok" value="OK" onclick="editclass();"/>
            </td>
        </tr>
    </table>
    <input type="hidden" name="classid1" id="classid1" value="<?php echo $c->superclass; ?>"/> <br/>
    <?php
    $tip = $_GET["tip"];
    if ($tip == 1) {
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
        $cid = $_GET['id'];
        $class = $DB->get_record('ontology_class', array('id' => $cid));
        $class->name = $name;
        $class->description = $_GET['opis'];
        $class->superclass = $_GET['id1'];
        $class->userid = $_GET['userid'];
        $class->course_modulesid = $_GET["moduleid"];
        if (is_Teacher())
            $class->status = '2';
        else
            $class->status = '1';
        $class->points = '0';

        $DB->update_record('ontology_class', $class);
    }
    ?>