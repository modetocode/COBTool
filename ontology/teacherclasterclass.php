<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
$id = $_GET['id']; //id na modulot
$classid = $_GET['classid']; //id na klasata za spojuvanje
?>
<div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest" style="width: 400px; margin-top: -40px;" >
    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
        <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center>
                <?php echo get_string('choose_the_merging_class', 'ontology'); ?>: </center></span>
    </div>
    <div class="ui-dialog-content ui-widget-content"> 
        <div>
            <?php
            echo class_hierarhy('lista', 'class_select', false, $id, $USER->id);
            ?>
        </div>
        <div>
            <br />
            <?php
            echo '<input type="button" id="nazad" onclick="nazad_spojuvanje()"  value="' . get_string('back', 'ontology') . '"/>';
            ?>

            &nbsp;

            <?php
            echo '<input type="button" id="next" onclick="confirm()" value="' . get_string('merging_the_classes', 'ontology') . '" style="visibility: hidden;"/>';
            ?>

        </div>
    </div></div>
<div>
    <input type="hidden" id="classid"/>
    <input type="hidden" id="classid2" value="<?php echo $classid ?>"/>
</div>