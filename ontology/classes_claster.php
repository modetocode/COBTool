<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
?>
<?php
/*
  echo 'do ovde </br>';
  if ($id) {
  $cm        = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
  $course    = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
  $ontology  = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
  } elseif ($n) {
  $ontology  = $DB->get_record('ontology', array('id' => $n), '*', MUST_EXIST);
  $course    = $DB->get_record('course', array('id' => $ontology->course), '*', MUST_EXIST);
  $cm        = get_coursemodule_from_instance('ontology', $ontology->id, $course->id, false, MUST_EXIST);
  } else {
  error('You must specify a course_module ID or an instance ID');
  }
  echo 'do ovde2 </br>';
  require_login($course, true, $cm); */


$ids = $_GET['ids'];
global $DB;
global $USER;
$uid = $USER->id;
//echo 'do ovde3 </br>';
//echo $ids."</br>";
$raw;
$names = array();
$names_rank = array();
$descrions = array();
$descrions_rank = array();
$superclasses = array();
$superclasses_rank = array();
$dlen = 0;
$slen = 0;
$sttl = 0;
$maxDescLen = 0;
$cmid = $_GET['ontid'];

foreach (explode(' ', $ids) as $tmp) {
//    echo '  klasa so id:'.$tmp;
    $raw = $DB->get_record('ontology_class', array('id' => $tmp));
    $ontologyid = $DB->get_record('course_modules', array('id' => $cmid));
    $ontologyid = $ontologyid->instance;
    $rank = $DB->get_record('ontology_student_rank', array('userid' => $raw->userid, 'ontologyid' => $ontologyid));
    $rank = $rank->rating;
    //sostavuvanje na niza so iminja
//    echo ' '.$raw->name;
//    echo '  prevaruvanje ime:'.array_search($raw->name,$names,true);
    if (!is_number(array_search($raw->name, $names, true))) {
//        echo '  dodadeno ime'.$raw->name;
        array_push($names, $raw->name);
        array_push($names_rank, $rank);
    } else {
        $names_rank[array_search($raw->name, $names, true)]+=$rank;
    }

    //sostavuvanje na niza so opisi
//    echo ' '.$raw->description;
//    echo '  prevaruvanje opis:'.array_search($raw->description,$descrions,true);
    if (!is_number(array_search($raw->description, $descrions, true))) {
//        echo '  dodaden opis'.$raw->description;
        $dlen++;
        array_push($descrions, $raw->description);
        if (mb_strlen($raw->description, "utf8") > $maxDescLen)
            $maxDescLen = mb_strlen($raw->description, "utf8");
        array_push($descrions_rank, $rank);
    }else {
        $descrions_rank[array_search($raw->description, $descrions, true)]+=$rank;
    }

    //sostavuvanje na niza so superklasi
    if (!is_number(array_search($raw->superclass, $superclasses, true))) {
        $slen++;
        array_push($superclasses, $raw->superclass);
        array_push($superclasses_rank, $rank);
    } else {
        $superclasses_rank[array_search($raw->superclass, $superclasses, true)]+=$rank;
    }
    $sttl+=$rank;
//    echo '</br>';
}

array_multisort($names_rank, SORT_NUMERIC, SORT_DESC, $names);
array_multisort($descrions_rank, SORT_NUMERIC, SORT_DESC, $descrions);
array_multisort($superclasses_rank, SORT_NUMERIC, SORT_DESC, $superclasses);

echo '<div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest"  style="margin-top: -40px;">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center>';
echo get_string('Classes_merge', 'ontology');
echo '</center></span>
                </div>
            <div class="ui-dialog-content ui-widget-content"> ';
echo '<b>' . get_string('Name', 'ontology') . ':</b> <br />';
echo '<input type="text" id="imeT" value="' . $names[0] . '" class=" ui-widget ui-state-hover" style="width:150px;" />  ';
echo '<select id="ime" onchange="ime_change()">';
foreach ($names as $name) {
    echo '<option value="' . $name . '">' . $name . '</option>';
}
echo '</select><br />';

echo '<b>' . get_string('Description', 'ontology') . ':</b><br />';
echo '<table style="margin-left:-6px"><tr><td colspan="2">';
echo '<input type="text" id="opisT" value="' . $descrions[0] . '" size="' . $maxDescLen . '" style="width:150px;" class=" ui-widget ui-state-hover"  /> </br>';
echo '</td></tr><tr><td>';
echo '<select id="opis" size=' . $dlen . ' onchange="opis_change()">';
$br = 0;
foreach ($descrions as $des) {
    if ($br == 0) {
        echo '<option selected=true value="' . $des . '">' . $des . '</option>';
    } else {
        echo '<option value="' . $des . '">' . $des . '</option>';
    }
    $br++;
}
echo '</select>';
echo '</td><td>';
echo '<select id="opisproc" size=' . $dlen . ' onchange="opisproc_change()">';
$br = 0;
foreach ($descrions_rank as $des) {
    if ($br == 0) {
        echo '<option selected=true value="' . $des . '">' . substr(100 * $des / $sttl, 0, 4) . '%</option>';
    } else {
        echo '<option value="' . $des . '">' . substr(100 * $des / $sttl, 0, 4) . '%</option>';
    }
    $br++;
}
echo '</select><br />';
echo '</td></tr></table>';

//status mora da im e 3
//superklasi
echo '<b>' . get_string('Superclass', 'ontology') . ':</b><br />';
$isselected = class_hierarhy3("superklasa", "superklasa_submit", 0, $cmid, $USER->id, $superclasses);
?>
<script language="javascript" type="text/javascript">
    document.getElementById("superklasa").size='<?php $slen ?>';
</script>
<?php
echo '</select><br />';

$brs = 0;
echo '<br /><b>' . get_string('Superclasses', 'ontology') . ':</b><br />';
echo '<form>';
$pom_superklass = array();
foreach (explode(' ', $ids) as $tmp) {

    $raws = $DB->get_records('ontology_class_expression', array('ontology_classid' => $tmp, 'type' => 1));
    foreach ($raws as $raw) {
        if (!is_number(array_search($raw->expression_text, $pom_superklass, true))) {
            array_push($pom_superklass, $raw->expression_text);
            echo '<input type="checkbox" name="cbox" value=' . $raw->id . ' /> ';
            echo get_expression_in_color($raw->expression);
            echo '</br>';
            $brs++;
        } else {
            
        }
    }
}
if ($brs == 0) {
    echo get_string('No_superclass_expressions', 'ontology');
}
echo '</form>';

//ekvivalentni klasi
echo '<b>' . get_string('Equivalent_Classes', 'ontology') . ':</b> <br />';
echo '<form>';
$bre = 0;
$pom_ekviv = array();
foreach (explode(' ', $ids) as $tmp) {
    $raws = $DB->get_records('ontology_class_expression', array('ontology_classid' => $tmp, 'type' => 2));
    foreach ($raws as $raw) {
        if (!is_number(array_search($raw->expression_text, $pom_ekviv))) {
            array_push($pom_ekviv, $raw->expression_text);
            echo '<input type="checkbox" name="cbox" value=' . $raw->id . ' /> ';
            echo get_expression_in_color($raw->expression);
            echo '</br>';
            $bre++;
        } else {
            
        }
    }
}
if ($bre == 0) {
    echo get_string('No_equivalent_class_expressions', 'ontology');
}
echo '</form>';

//disjunktni klasi
echo '<b>' . get_string('Disjoint_Classes', 'ontology') . ':</b> <br />';
echo '<form>';
$brd = 0;
$pom_disj = array();
foreach (explode(' ', $ids) as $tmp) {
    $raws = $DB->get_records('ontology_class_expression', array('ontology_classid' => $tmp, 'type' => 3));
    foreach ($raws as $raw) {
        if (!is_number(array_search($raw->expression_text, $pom_disj))) {
            array_push($pom_disj, $raw->expression_text);
            echo '<input type="checkbox" name="cbox" value=' . $raw->id . ' /> ';
            echo get_expression_in_color($raw->expression);
            echo '</br>';
            $brd++;
        } else {
            
        }
    }
}
if ($brd == 0) {
    echo get_string('No_disjoint_class_expressions', 'ontology');
}
echo '</form>';
?>
<button onclick="canceled()"> <?php echo get_string('Cancel', 'ontology'); ?> </button>
<button id="classes_write" 
<?php if (!$isselected) echo "style=\"visibility: hidden;\""; ?> onclick="writeClassToDB()"> <?php echo get_string('Save', 'ontology'); ?></button>
</div></div>
<input type="hidden" name="moduleid" value="<?php echo $cmid; ?>" id="moduleid"/>
<input type="hidden" name="userid" value="<?php echo $uid; ?>" id="userid"/>
<input type="hidden" name="site_ida" value="<?php echo $ids; ?>" id="site_ida"/>
<div id="refresh">
</div>
</div>

<style>
    .latest { overflow:visible; position:static; }
</style>