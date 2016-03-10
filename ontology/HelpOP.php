<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Prints a particular instance of newmodule
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_newmodule
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/// (Replace newmodule with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // newmodule instance ID - it should be named as the first character of the module
$PAGE->requires->js('/mod/ontology/js/jquery-1.5.1.min.js', true);
$PAGE->requires->js('/mod/ontology/js/jquery-ui-1.8.14.custom.min.js', true);
$PAGE->requires->css('/mod/ontology/css/redmond/jquery-ui-1.8.14.custom.css', true);
if ($id) {
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $ontology = $DB->get_record('ontology', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $ontology->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('ontology', $ontology->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/ontology/view.php', array('id' => $cm->id));
$PAGE->set_title($ontology->name);
$PAGE->set_heading($course->shortname);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
// Output starts here
echo $OUTPUT->header();
echo '<div class="ui-widget-header ui-corner-all">';
echo $OUTPUT->heading(get_string('Explanation_of_terms', 'ontology'));
echo '</div> <br/>';
?>
<script>
    $(function() {
        $("#accordion").accordion({
            event: "click hoverintent"
        });
        $("input:button").button();
    });
	
    var cfg = ($.hoverintent = {
        sensitivity: 7,
        interval: 100
    });

    $.event.special.hoverintent = {
        setup: function() {
            $( this ).bind( "mouseover", jQuery.event.special.hoverintent.handler );
        },
        teardown: function() {
            $( this ).unbind( "mouseover", jQuery.event.special.hoverintent.handler );
        },
        handler: function( event ) {
            event.type = "hoverintent";
            var self = this,
            args = arguments,
            target = $( event.target ),
            cX, cY, pX, pY;
			
            function track( event ) {
                cX = event.pageX;
                cY = event.pageY;
            };
            pX = event.pageX;
            pY = event.pageY;
            function clear() {
                target
                .unbind( "mousemove", track )
                .unbind( "mouseout", arguments.callee );
                clearTimeout( timeout );
            }
            function handler() {
                if ( ( Math.abs( pX - cX ) + Math.abs( pY - cY ) ) < cfg.sensitivity ) {
                    clear();
                    jQuery.event.handle.apply( self, args );
                } else {
                    pX = cX;
                    pY = cY;
                    timeout = setTimeout( handler, cfg.interval );
                }
            }
            var timeout = setTimeout( handler, cfg.interval );
            target.mousemove( track ).mouseout( clear );
            return true;
        }
    };
</script>
<script>
    $(function() {
        var icons = {
            header: "ui-icon-circle-arrow-e",
            headerSelected: "ui-icon-circle-arrow-s"
        };
        $( "#accordion" ).accordion({
            icons: icons
        });
        $( "#toggle" ).button().toggle(function() {
            $( "#accordion" ).accordion( "option", "icons", false );
        }, function() {
            $( "#accordion" ).accordion( "option", "icons", icons );
        });
    });
</script>
<script type="text/javascript">
    function nazad(){
        window.location= <?php echo "\"oproperties.php?id=" . $id . "\"" ?>
    }
</script>
<div id="accordion">
    <h3><a href="#"><?php echo get_string('Properties', 'ontology'); ?></a></h3>
    <div>
        <p align="left"><b><font color="#006FFF"> <?php echo get_string('Property', 'ontology'); ?> </font></b><?php echo ' ' . get_string('Property_definition', 'ontology'); ?></p>
        <p align="left"><b><font color="#006FFF"><?php echo get_string('Oproperties', 'ontology'); ?></font></b><?php echo ' ' . get_string('Oproperties_definition', 'ontology'); ?></p>
        <p align="left"><b><font><?php echo get_string('Domain', 'ontology'); ?></font></b><?php echo ' ' . get_string('Domain_definition', 'ontology'); ?></p> 
        <p align="left"><b><font><?php echo get_string('Range', 'ontology'); ?></font></b><?php echo ' ' . get_string('Range_OP_definition', 'ontology'); ?></p>


    </div>
    <h3><a href="#"><?php echo get_string('Characteristics_of_the_properties', 'ontology'); ?></a></h3>
    <div>
        <p align="left"><b><font color="#006FFF"> <?php echo get_string('Equivalent_Properties', 'ontology'); ?></font></b><?php echo ' ' . get_string('Equivalent_Properties_definition', 'ontology'); ?></p> 
        <p align="left"><b><font color="#006FFF"><?php echo get_string('Disjoint_Properties', 'ontology'); ?></font></b><?php echo ' ' . get_string('Disjoint_Properties_definition', 'ontology'); ?></p>

        <ul type="square">

            <li> <?php echo get_string('inverse_property_definition', 'ontology'); ?> 

            <li> <?php echo get_string('OP_functional_property_definition', 'ontology'); ?> 
            <li> <?php echo get_string('inverse-functional_property_definition', 'ontology'); ?>  
            <li> <?php echo get_string('transitive_property_definition', 'ontology'); ?>  
            <li> <?php echo get_string('symmetric_property_definition', 'ontology'); ?>   
            <li> <?php echo get_string('asymmetric_property_definition', 'ontology'); ?>  

            <li> <?php echo get_string('reflexively_property_definition', 'ontology'); ?>  
            <li> <?php echo get_string('irreflexive_property_definition', 'ontology'); ?> 

                </div>
                <h3><a href="#"><?php echo get_string('Additional_explanations', 'ontology'); ?></a></h3>
                <div>
                    <p ><?php echo get_string('Term_black_color', 'ontology'); ?></p>

                    <p ><?php echo get_string('Term_red_color', 'ontology'); ?></p>

                    <p ><?php echo get_string('Term_green_color', 'ontology'); ?></p>

                    <p ><?php echo get_string('Term_pink_color', 'ontology'); ?></p>

                    <p ><?php echo get_string('Cannot_delete_oproperty_with_subproperty', 'ontology'); ?></p>

                </div>
                <h3><a href="#"><?php echo get_string('Grading', 'ontology'); ?></a></h3>
                <div>
                    <p ><?php echo get_string('Number_of_points', 'ontology'); ?></p>

                </div>
                </div>
                <br />
                <div>
                    <input type="button"  value="<?php echo get_string('Back', 'ontology'); ?>" onclick="nazad();"/>
                </div>
<?php
// Finish the page
echo $OUTPUT->footer();
?>