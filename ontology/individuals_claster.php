<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

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
$dlen = 0;
$slen = 0;
$sttl = 0;
$maxDescLen = 0;
$cmid = $_GET['ontid'];

foreach (explode(' ', $ids) as $tmp) {
//    echo '  klasa so id:'.$tmp;
    $raw = $DB->get_record('ontology_individual', array('id' => $tmp));
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
        array_push($descrions_rank, $rank);
    } else {
        $descrions_rank[array_search($raw->description, $descrions, true)]+=$rank;
    }
    $sttl+=$rank;
//    echo '</br>';
}

array_multisort($names_rank, SORT_NUMERIC, SORT_DESC, $names);
array_multisort($descrions_rank, SORT_NUMERIC, SORT_DESC, $descrions);

echo '<div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest"  style="margin-top: -40px; width:300px">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center>';

echo get_string('Individuals_merge', 'ontology');

echo '</center></span>
                </div>
            <div class="ui-dialog-content ui-widget-content"> ';
echo '<b>' . get_string('Name', 'ontology') . ':</b><br />';
echo '<input type="text" id="imeT" value="' . $names[0] . '" class=" ui-widget ui-state-hover" style="width:150px;"> ';
echo '<select id="ime" onchange="ime_change()">';
foreach ($names as $name) {
    echo '<option value="' . $name . '">' . $name . '</option>';
}
echo '</select>';

echo '<br /><b>' . get_string('Description', 'ontology') . ':</b><br />';
echo '<table style="margin-left:-6px><tr><td colspan="2">';
echo '<input type="text" id="opisT" value="' . $descrions[0] . '" size="' . $maxDescLen . '" class=" ui-widget ui-state-hover" style="width:150px;"> </br>';
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
echo '</select>';
echo '</td></tr></table>';



$brs = 0;
echo '<b>' . get_string('Instance_class', 'ontology') . ':</b><br />';
echo '<form>';
$pom_superklass = array();
foreach (explode(' ', $ids) as $tmp) {

    $raws = $DB->get_records('ontology_individual_expression', array('ontology_individualid' => $tmp));
    foreach ($raws as $raw) {
        if (!is_number(array_search($raw->expression_text, $pom_superklass, true))) {
            array_push($pom_superklass, $raw->expression_text);
            echo '<input type="checkbox" name="i_cbox" value=' . $raw->id . ' /> ';
            echo get_expression_in_color($raw->expression);
            echo '</br>';
            $brs++;
        } else {
            
        }
    }
}
if ($brs == 0) {
    echo get_string('No_classes', 'ontology');
}
echo '</form>';

//ekvivalentni klasi
echo '<br /><b>' . get_string('Oproperties', 'ontology') . ':</b>';
echo '<form>';
$bre = 0;
$pom_obj = array();
foreach (explode(' ', $ids) as $tmp) {
    $raws = $DB->get_records('ontology_individual_property_individual', array('ontology_individualid' => $tmp));
    foreach ($raws as $raw) {
        if (!is_number(array_search(get_name_of_oproperty($raw->ontology_propertyid) . get_name_of_individual($raw->ontology_individualid2), $pom_obj))) {
            array_push($pom_obj, get_name_of_oproperty($raw->ontology_propertyid) . get_name_of_individual($raw->ontology_individualid2));
            echo '<input type="checkbox" name="o_cbox" value=' . $raw->id . ' /> ';
            echo get_name_of_individual($tmp) . ' ' . get_name_of_oproperty($raw->ontology_propertyid) . ' ' . get_name_of_individual($raw->ontology_individualid2);
            echo '</br>';
            $bre++;
        } else {
            
        }
    }
}
if ($bre == 0) {
    echo get_string('No_oproperties', 'ontology');
}
echo '</form>';

//disjunktni klasi
echo '<br /><b>' . get_string('Dproperties', 'ontology') . ':</b>';
echo '<form>';
$brd = 0;
$pom_dat = array();
foreach (explode(' ', $ids) as $tmp) {
    $raws = $DB->get_records('ontology_individual_property_data', array('ontology_individualid' => $tmp));
    foreach ($raws as $raw) {
        if (!is_number(array_search(get_name_of_dproperty($raw->ontology_propertyid) . $raw->data, $pom_dat))) {
            array_push($pom_dat, get_name_of_dproperty($raw->ontology_propertyid) . $raw->data);
            echo '<input type="checkbox" name="p_cbox" value=' . $raw->id . ' /> ';
            echo get_name_of_individual($tmp) . ' ' . get_name_of_dproperty($raw->ontology_propertyid) . ' ' . $raw->data;
            echo '</br>';
            $brd++;
        } else {
            
        }
    }
}
if ($brd == 0) {
    echo get_string('No_dproperties', 'ontology');
}
echo '</form>';
?>

<button onclick="canceled()"> <?php echo get_string('Cancel', 'ontology'); ?> </button>
<button id="write_btn" onclick="writeIndividualToDB()"> <?php echo get_string('Save', 'ontology'); ?></button><br /></div></div>
<div id="ff">
    <input type="hidden" name="moduleid" value="<?php echo $cmid; ?>" id="moduleid"/>
    <input type="hidden" name="userid" value="<?php echo $uid; ?>" id="userid"/>
    <input type="hidden" name="site_ida" value="<?php echo $ids; ?>" id="site_ida"/>
    <div id="refresh">
    </div>
</div>

<style>
    .latest { overflow:visible; position:static; }
</style>
