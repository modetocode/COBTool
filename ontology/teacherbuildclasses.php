
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
$PAGE->requires->js('/mod/ontology/js/jquery-1.5.1.min.js', true);
$PAGE->requires->js('/mod/ontology/js/jquery-ui-1.8.14.custom.min.js', true);
$PAGE->requires->css('/mod/ontology/css/redmond/jquery-ui-1.8.14.custom.css', true);
$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // newmodule instance ID - it should be named as the first character of the module

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
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'ontology')));

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
// Output starts here
echo $OUTPUT->header();
echo '<div class="ui-widget-header ui-corner-all">';
echo $OUTPUT->heading(get_string('choise_of_classes_for_ontology', 'ontology'));
echo '</div> <br/>';
?>
<script>
    $(function() {
        $("input:button").button();
        $("input:text").button();
        $( "a",".links").button();
    });
</script>
<script type="text/javascript">

    function ajax()
    {
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4)
            {
                document.getElementById("Region1").innerHTML=xmlhttp.responseText;
                setStyle();
            }
        }
        if (document.getElementById("prikaz").value==1)
            xmlhttp.open("GET","teacherclass.php?id="+document.getElementById("id").value+"&tip=1&klaster="+document.getElementById("stekk").value,true);
        else
            xmlhttp.open("GET","teacherclass.php?id="+document.getElementById("id").value+"&tip=2&klaster="+document.getElementById("stekk").value,true);
        xmlhttp.send();
    }
    function ajax_spoj(id)
    {
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4)
            {
                document.getElementById("Region1").innerHTML=xmlhttp.responseText;
                setStyle();
            }
        }
        xmlhttp.open("GET","teacherclasterclass.php?id="+document.getElementById("id").value+"&classid="+document.getElementById("klasi"+id).value,true);
        xmlhttp.send();
    }
    function Prikaz()
    {
        ajax();
    }
    function insertClass(value)
    {

        ajax_write(1,value); 
    }

    function deleteClass(value)
    {
        ajax_write(2,value);
    }

    function updateClass(value)
    {
        ajax_write(3,value);
    }

    function updateDeleteClass(value)
    {
        ajax_write(4,value);
    }
    function parserRedirect()
    {
        var ids=document.getElementById("stekk").value;
        var ontid=document.getElementById("id").value;
        window.location= "classes_claster.php?ids="+ids+"&ontid="+ontid;
    }
    function klasterClass(value)
    {
        var str=value.id;
        var id=str.substr(7);
        var classes=document.getElementById("klasi"+id).value;
        if (document.getElementById("stekk").value=="")
            document.getElementById("stekk").value=classes;
        else
            document.getElementById("stekk").value=document.getElementById("stekk").value+" "+classes;
        ajax();
    }
    function spojClass(value)
    {   document.getElementById("heading").style.visibility="Hidden";
        document.getElementById("prikaz").style.visibility="Hidden";
        var str=value.id;
        var id=str.substr(4);
        var classes=document.getElementById("klasi"+id).value;
        if (document.getElementById("stekk").value=="")
            document.getElementById("stekk").value=classes;
        else
            document.getElementById("stekk").value=document.getElementById("stekk").value+" "+classes;
        ajax_spoj(id);
    }
    function ajax_write(tip,value)
    {
    
        //zapishuvanje na podatocite za klasite
    
        // tip == 1  - dodavanje na nova klasa
        // tip == 2  - brishenje na nova klasa
        // tip == 3  - azuriranje na postoecka klasa
        // tip == 4  - brishenje na azuriranjata na postoecka klasa
    
        //value e vrednosta na stisnatoto kopce
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4)
            {
                document.getElementById("WriteRegion").innerHTML=xmlhttp.responseText;
            }
        }
        if (tip==1)
        {  //dodavanje na nova klasa
            var str=value.id;
            var id="opisk"+str.substr(3);
            var classes=document.getElementById("klasi"+str.substr(3)).value;
            var oRadio=document.klasi.elements[id.toString()];
            var classid="";
        
            if (oRadio[0])
            {
                for(var i = 0; i < oRadio.length; i++)
                {
                    if(oRadio[i].checked)
                    {
                        classid=oRadio[i].value;
                        break;
                    }
                }
            }
            else
                classid=oRadio.value;
    
            var id2="superk"+str.substr(3);
            var oCheck1=document.klasi.elements[id2];
            var string="";
            if (oCheck1!=null)
            {
                if (oCheck1[0])
                {
                    for(var i = 0; i < oCheck1.length; i++)
                    {
                        if(oCheck1[i].checked)
                        {
                            if (string!="")
                                string=string+" "+oCheck1[i].value;
                            else
                                string=oCheck1[i].value;    
                        }
                    }
                }
                else
                    if (oCheck1.checked)
                        string=oCheck1.value;
            }
            var id3="ekvik"+str.substr(3);
            var oCheck2=document.klasi.elements[id3];
            var string2="";
            if (oCheck2!=null)
            {
                if (oCheck2[0])
                {
                    for(var i = 0; i < oCheck2.length; i++)
                    {
                        if(oCheck2[i].checked)
                        {
                            if (string2!="")
                                string2=string2+" "+oCheck2[i].value;
                            else
                                string2=oCheck2[i].value;    
                        }
                    }
                }
                else
                    if (oCheck2.checked)
                        string2=oCheck2.value;
            }
            var id4="disjk"+str.substr(3);
            var oCheck3=document.klasi.elements[id4];
            var string3="";
            if (oCheck3!=null)
            {
                if (oCheck3[0])
                {
                    for(var i = 0; i < oCheck3.length; i++)
                    {
                        if(oCheck3[i].checked)
                        {
                            if (string3!="")
                                string3=string3+" "+oCheck3[i].value;
                            else
                                string3=oCheck3[i].value;    
                        }
                    }
                }
                else
                    if (oCheck3.checked)
                        string3=oCheck3.value;
            } 
            xmlhttp.open("GET","teacherwrite.php?tip=1&id="+classid+"&classes="+classes+"&superk="+string+"&ekvik="+string2+"&disjk="+string3,true);
        }
        else
            if (tip==2)
        { //brishenje na nova klasa
            var str=value.id;
            var classes=document.getElementById("klasi"+str.substr(3)).value;
            var delexpressions=document.getElementById("brishi"+str.substr(3)).value;
            var delexpressions2=document.getElementById("brishi2"+str.substr(3)).value;
            var delexpressions3=document.getElementById("brishi3"+str.substr(3)).value;
            xmlhttp.open("GET","teacherdelete.php?tip=1&classes="+classes+"&delexpressions="+delexpressions+"&delexpressions2="+delexpressions2+"&delexpressions3="+delexpressions3,true);
        }
        else
            if (tip==3)
        { //azuriranje na postoecka klasa
            var str=value.id;
            var id="updatek"+str.substr(6);
            var oCheck1=document.klasi.elements[id];
            var id2="changeSC"+str.substr(6);
            var oCheck2=document.klasi.elements[id2];
            var string="";
            var string2="";
            var string3="";
            if (oCheck1!=null)
            {
                if (oCheck1[0])
                {
                    for(var i = 0; i < oCheck1.length; i++)
                    {
                        if(oCheck1[i].checked)
                        {
                            if (string!="")
                                string=string+" "+oCheck1[i].value;
                            else
                                string=oCheck1[i].value;    
                        }
                        else
                        {
                            if (string2!="")
                                string2=string2+" "+oCheck1[i].value;
                            else
                                string2=oCheck1[i].value;  
                        }
                    }
                }
                else
                    if (oCheck1.checked)
                        string=oCheck1.value;
                else
                    string2=oCheck1.value;
            }
            if (oCheck2!=null)
            {
                for(var i = 0; i < oCheck2.length; i++)
                {
                    if(oCheck2[i].checked)
                    {
                        string3=oCheck2[i].value;
                    }
                }
            }
            xmlhttp.open("GET","teacherwriteupdate.php?tip=1&accepted="+string+"&refused="+string2+"&superclassExp="+string3,true);
        }
        else
        { //brisenje na azuriranja na celata klasa
            
            var str=value.id;
                 
            var id="updatek"+str.substr(6);
            var oCheck1=document.klasi.elements[id];
            var string="";
            if (oCheck1!=null)
            {
                if (oCheck1[0])
                {
                    for(var i = 0; i < oCheck1.length; i++)
                    {
                        if (string!="")
                            string=string+" "+oCheck1[i].value;
                        else
                            string=oCheck1[i].value;    
                    }
                }
                else
                    string=oCheck1.value;
            }
            xmlhttp.open("GET","teacherdeleteupdate.php?tip=1&expressions="+string,true);
        }
        xmlhttp.send();
        Prikaz();
        setTimeout("Prikaz()",200);
    
    }


    function KlasteriraniKlasi(){
        document.getElementById("heading").style.visibility="Hidden";
        document.getElementById("prikaz").style.visibility="Hidden";
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4)
            {
                document.getElementById("Region1").innerHTML=xmlhttp.responseText;
                setStyle();
            }
        }
        var ids=document.getElementById("stekk").value;
        var ontid=document.getElementById("id").value;
        xmlhttp.open("GET","classes_claster.php?ids="+ids+"&ontid="+ontid,true);
        xmlhttp.send();
    
    }
    function setStyle()
    {
        $("button").button();
        $("input:button").button();
    }

    function getSelectedCheckbox(buttonGroup) {
        //za zemanje na id na site selektirani chekbox-i
        var retArr = new Array();
        var lastElement = 0;
        if (buttonGroup[0]) { // ako e niza
            for (var i=0; i<buttonGroup.length; i++) {
                if (buttonGroup[i].checked) {
                    retArr.length = lastElement;
                    retArr[lastElement] = i;
                    lastElement++;
                }
            }
        } else { // ako e samo edno
            if (buttonGroup.checked) {
                retArr.length = lastElement;
                retArr[lastElement] = 0;
            }
        }
        return retArr;
    } // Ends the "getSelectedCheckbox" function

    function getSelectedCheckboxValue(buttonGroup) {
        // niza od vrednosti na chekboxi
        var retArr = new Array(); 
        var selectedItems = getSelectedCheckbox(buttonGroup);
        if (selectedItems.length != 0) { // ako ima bar edno selektirano
            retArr.length = selectedItems.length;
            for (var i=0; i<selectedItems.length; i++) {
                if (buttonGroup[selectedItems[i]]) { //ako e niza (a ne samo edno)
                    retArr[i] = buttonGroup[selectedItems[i]].value;
                } else {
                    retArr[i] = buttonGroup.value;
                }
            }
        }
        return retArr;
    } // Ends the "getSelectedCheckBoxValue" function

    function writeClassToDB(){
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if (xmlhttp.readyState==4)
            {
                document.getElementById("refresh").innerHTML=xmlhttp.responseText;
            }
        }
        var ida=getSelectedCheckboxValue(document.getElementsByName("cbox"));

        xmlhttp.open("GET","classes_claster_write.php?uid="+document.getElementById("userid").value+"&mid="+document.getElementById("moduleid").value+"&ime="+encodeURIComponent(document.getElementById("imeT").value)+"&opis="+encodeURIComponent(document.getElementById("opisT").value)+"&superklasaID="+document.getElementById("superklasa").value+"&izraziIDa="+ida+"&site_ida="+document.getElementById("site_ida").value,true);
        xmlhttp.send();
        refresh();
    
    }

    function refresh()
    {
        document.getElementById("stekk").value="";
        document.getElementById("heading").style.visibility="Visible";
        document.getElementById("prikaz").style.visibility="Visible";
        setTimeout("Prikaz()",200);
    }

    function nazad_pregleduvanje()
    {
        window.location= <?php echo "\"teacherstudents.php?id=" . $id . "\"" ?>
    }

    function opis_change()
    {
        var opisText = document.getElementById("opis").value;
        document.getElementById("opisT").value=opisText;
        document.getElementById("opisproc").selectedIndex=document.getElementById("opis").selectedIndex;
    }

    function opisproc_change()
    {
        document.getElementById("opis").selectedIndex=document.getElementById("opisproc").selectedIndex;
        var opisText = document.getElementById("opis").value;
        document.getElementById("opisT").value=opisText;
    }

    function ime_change(){
        var opisText = document.getElementById("ime").value;
        document.getElementById("imeT").value=opisText;    
    }

    function superklasa_submit(prazno){
        if(prazno.value!="")
        {
            document.getElementById("classes_write").style.visibility="visible";
        }
    }

    function canceled()
    {
        window.location= <?php echo "\"teacherbuildclasses.php?id=" . $id . "\"" ?>
    }

    function class_select(list){
        document.getElementById("classid").value=list.value;
        document.getElementById("next").style.visibility="visible";
    }

    function nazad_spojuvanje()
    {
        window.location= <?php echo "\"teacherbuildclasses.php?id=" . $id . "\"" ?>
    }

    function confirm(){
        var id=document.getElementById("classid").value;
        var id2=document.getElementById("classid2").value;
        document.getElementById("stekk").value=id2+" "+id;
        KlasteriraniKlasi();
    }


    function azuriranje_superklasa(order)
    {   
        $('.superklasa'+order+'').remove();
        if($('input[id^="updatek"]').length>0){
            $("#izrazi_za_ekvivalentni"+order+'').before(
            "<tr class='superklasa"+order+"'> <td> </td> <td> <b> Менување на надкласа: </b> </td> <td> </td> </tr>"
        )
            $("#izrazi_za_ekvivalentni"+order+'').before(
            "<tr class='superklasa"+order+"'> <td> </td> <td> <input type='radio' name='changeSC"+order+"' value='-1' checked='true' /><span style='color: #FF9D00'>"+$('#nadklasa'+order).text()+"</span><span> (не прави промена)</span> </td> <td> </td> </tr>"
        )
            var kolku=0;
            var site=$('input[id^="updatek'+order+'"]');
            for(var i=0;i<site.length;i++){
                var span=$('#name'+site[i].value)[0];
                if(site[i].checked==true){
                    var pass=0;
                    var expression=span.title;
                    if(expression != undefined){
                        if(expression.charAt(1)=='k' && expression.charAt(0)=='^'){
                            if(!isNaN(expression.substring(2,expression.length-2))){
                                pass=1;
                            }
                        }
                    }
                    if(pass==1){
                        $("#izrazi_za_ekvivalentni"+order+'').before(
                        "<tr class='superklasa"+order+"'> <td> </td> <td> <input type='radio' name='changeSC"+order+"' value='"+expression.substring(2,expression.length-2)+"' />"+span.innerHTML+"</td> <td> </td> </tr>"
                    );
                        kolku++;
                    }
                }
            }
            if(kolku==0){
                $('.superklasa'+order+'').remove();
            }
        }
    }
</script>
<table><tr><td>
            <b id="heading"> <?php echo get_string('sort', 'ontology'); ?> </b>
            <select id="prikaz" onchange="Prikaz();">
                <option value="1"> <?php echo get_string('by_rating', 'ontology'); ?> </option>
                <option value="2"> <?php echo get_string('alphabetical', 'ontology'); ?></option>
            </select>
        </td></tr></table>
<div id="Region1">
</div>
<input type="hidden" value="<?php echo $id ?>" id="id" />
<input type="hidden" value="" id="stekk"/>
<div id="WriteRegion">

</div>
<script>
    window.onload=ajax ;
</script>
<?php
echo $OUTPUT->footer();