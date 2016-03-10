<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
$id = $_GET['id']; //id na modulot
$propertyid = $_GET['propertyid']; //id na svojstvoto za spojuvanje
?>
<div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest" style="width: 400px; margin-top: -40px;" >
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
        <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center>
                <?php
                echo get_string('choose_the_merging_property', 'ontology');
                ?>
                : </center></span>
    </div>
    <div class="ui-dialog-content ui-widget-content"> 
        <div>
            <?php
            echo data_property_hierarhy('lista', 'property_select', false, $id, $USER->id, false);
            ?>
        </div>
        <div>
            <br />
            <input type="button" id="nazad" onclick="nazad_spojuvanje()"  value="<?php echo get_string('back', 'ontology'); ?>"/> &nbsp;
            <input type="button" id="next" onclick="confirm()" value="<?php echo get_string('merging_the_properties', 'ontology'); ?>" style="visibility: hidden;"/>


        </div></div></div>
<div>
    <input type="hidden" id="propertyid"/>
    <input type="hidden" id="propertyid2" value="<?php echo $propertyid ?>"/>
</div>