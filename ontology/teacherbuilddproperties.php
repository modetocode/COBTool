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
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'ontology')));

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
// Output starts here
echo $OUTPUT->header();
echo '<div class="ui-widget-header">';
echo $OUTPUT->heading(get_string('choise_of_data_properties_for_ontology', 'ontology'));
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
            xmlhttp.open("GET","teacherdproperty.php?id="+document.getElementById("id").value+"&tip=1&klaster="+document.getElementById("stekk").value,true);
        else
            xmlhttp.open("GET","teacherdproperty.php?id="+document.getElementById("id").value+"&tip=2&klaster="+document.getElementById("stekk").value,true);
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
        xmlhttp.open("GET","teacherclasterdproperty.php?id="+document.getElementById("id").value+"&propertyid="+document.getElementById("svojs"+id).value,true);
        xmlhttp.send();
    }

    function Prikaz()
    {
        ajax();
    }
    function insertProperty(value)
    {
        ajax_write(1,value); 
    }

    function deleteProperty(value)
    {
        ajax_write(2,value);
    }

    function updateProperty(value)
    {
        ajax_write(3,value);
    }

    function updateDeleteProperty(value)
    {
        ajax_write(4,value);
    }
    function parserRedirect()
    {
        var ids=document.getElementById("stekk").value;
        var ontid=document.getElementById("id").value;
        window.location= "dproperties_claster.php?ids="+ids+"&ontid="+ontid;
    }
    function klasterProperty(value)
    {
        var str=value.id;
        var id=str.substr(7);
        var properties=document.getElementById("svojs"+id).value;
        if (document.getElementById("stekk").value=="")
            document.getElementById("stekk").value=properties;
        else
            document.getElementById("stekk").value=document.getElementById("stekk").value+" "+properties;
        ajax();
    }
    function spojProperty(value)
    {   document.getElementById("heading").style.visibility="Hidden";
        document.getElementById("prikaz").style.visibility="Hidden";
        var str=value.id;
        var id=str.substr(4);
        var properties=document.getElementById("svojs"+id).value;
        if (document.getElementById("stekk").value=="")
            document.getElementById("stekk").value=properties;
        else
            document.getElementById("stekk").value=document.getElementById("stekk").value+" "+properties;
        ajax_spoj(id);
    }
    function ajax_write(tip,value)
    {
    
        //zapishuvanje na podatocite za svojstvata
    
        // tip == 1  - dodavanje na novo svojstvo
        // tip == 2  - brishenje na novo svojstvo
        // tip == 3  - azuriranje na postoecko svojstvo
        // tip == 4  - brishenje na azuriranjata na postoecko svojstvo
    
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
        {  //dodavanje na novo svojstvo
            var str=value.id;
            var id="opisk"+str.substr(3);
            var properties=document.getElementById("svojs"+str.substr(3)).value;
            var oRadio=document.svojstva.elements[id.toString()];
            var propertyid="";
        
            if (oRadio[0])
            {
                for(var i = 0; i < oRadio.length; i++)
                {
                    if(oRadio[i].checked)
                    {
                        propertyid=oRadio[i].value;
                        break;
                    }
                }
            }
            else
                propertyid=oRadio.value;
    
            var id2="domeni"+str.substr(3);
            var oCheck1=document.svojstva.elements[id2];
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
            var id4="ekviv"+str.substr(3);
            var oCheck3=document.svojstva.elements[id4];
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
            var id5="disjk"+str.substr(3);
            var oCheck4=document.svojstva.elements[id5];
            var string4="";
            if (oCheck4!=null)
            {
                if (oCheck4[0])
                {
                    for(var i = 0; i < oCheck4.length; i++)
                    {
                        if(oCheck4[i].checked)
                        {
                            if (string4!="")
                                string4=string4+" "+oCheck4[i].value;
                            else
                                string4=oCheck4[i].value;    
                        }
                    }
                }
                else
                    if (oCheck4.checked)
                        string4=oCheck4.value;
            }
            xmlhttp.open("GET","teacherwrite.php?tip=3&id="+propertyid+"&properties="+properties+"&domeni="+string+"&ekvivalentni="+string3+"&disjunktni="+string4,true);
        }
        else
            if (tip==2)
        { //brishenje na novo svojstvo
            var str=value.id;
            var properties=document.getElementById("svojs"+str.substr(3)).value;
            var delexpressions=document.getElementById("brishi"+str.substr(3)).value;
            var delexpressions2=document.getElementById("brishi2"+str.substr(3)).value;
            var delexpressions3=document.getElementById("brishi3"+str.substr(3)).value;
            xmlhttp.open("GET","teacherdelete.php?tip=3&properties="+properties+"&delexpressions="+delexpressions+"&delexpressions2="+delexpressions2+"&delexpressions3="+delexpressions3,true);
        }
        else
            if (tip==3)
        { //azuriranje na postoecko svojstvo
            var str=value.id;
            var id="updatek"+str.substr(6);
            var oCheck1=document.svojstva.elements[id];
            var string="";
            var string2="";
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
                
            var id2="updateq"+str.substr(6);
            var oCheck2=document.svojstva.elements[id2];
            var string3="";
            var string4="";
            if (oCheck2!=null)
            {
                if (oCheck2[0])
                {
                    for(var i = 0; i < oCheck2.length; i++)
                    {
                        if(oCheck2[i].checked)
                        {
                            if (string3!="")
                                string3=string3+" "+oCheck2[i].value;
                            else
                                string3=oCheck2[i].value;    
                        }
                        else
                        {
                            if (string4!="")
                                string4=string4+" "+oCheck2[i].value;
                            else
                                string4=oCheck2[i].value;  
                        }
                    }
                }
                else
                    if (oCheck2.checked)
                        string3=oCheck2.value;
                else
                    string4=oCheck2.value;
            }
                
            var id3="updated"+str.substr(6);
            var oCheck3=document.svojstva.elements[id3];
            var string5="";
            var string6="";
            if (oCheck3!=null)
            {
                if (oCheck3[0])
                {
                    for(var i = 0; i < oCheck3.length; i++)
                    {
                        if(oCheck3[i].checked)
                        {
                            if (string5!="")
                                string5=string5+" "+oCheck3[i].value;
                            else
                                string5=oCheck3[i].value;    
                        }
                        else
                        {
                            if (string6!="")
                                string6=string6+" "+oCheck3[i].value;
                            else
                                string6=oCheck3[i].value;  
                        }
                    }
                }
                else
                    if (oCheck3.checked)
                        string5=oCheck3.value;
                else
                    string6=oCheck3.value;
            }
                
            xmlhttp.open("GET","teacherwriteupdate.php?tip=3&accepted="+string+"&refused="+string2+"&accepted2="+string3+"&refused2="+string4+"&accepted3="+string5+"&refused3="+string6,true);
        }
        else
        { //brisenje na azuriranja na celoto svojstvo
            
            var str=value.id;
                 
            var id="updatek"+str.substr(6);
            var oCheck1=document.svojstva.elements[id];
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
                
            var id2="updateq"+str.substr(6);
            var oCheck2=document.svojstva.elements[id2];
            var string2="";
            if (oCheck2!=null)
            {
                if (oCheck2[0])
                {
                    for(var i = 0; i < oCheck2.length; i++)
                    {
                        if (string2!="")
                            string2=string2+" "+oCheck2[i].value;
                        else
                            string2=oCheck2[i].value;    
                    }
                }
                else
                    string2=oCheck2.value;
            }
                
            var id3="updated"+str.substr(6);
            var oCheck3=document.svojstva.elements[id3];
            var string3="";
            if (oCheck3!=null)
            {
                if (oCheck3[0])
                {
                    for(var i = 0; i < oCheck3.length; i++)
                    {
                        if (string3!="")
                            string3=string3+" "+oCheck3[i].value;
                        else
                            string3=oCheck3[i].value;    
                    }
                }
                else
                    string3=oCheck3.value;
            }
                
            xmlhttp.open("GET","teacherdeleteupdate.php?tip=3&expressions="+string+"&expressions2="+string2+"&expressions3="+string3,true);
        }
        xmlhttp.send();
        Prikaz();
        setTimeout("Prikaz()",200);
    
    }


    function KlasteriraniPSvojstva(){
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
                // document.getElementById("lista").destroy();
                document.getElementById("Region1").innerHTML=xmlhttp.responseText;
                setStyle();
            }
        }
        var ids=document.getElementById("stekk").value;
        var ontid=document.getElementById("id").value;
        xmlhttp.open("GET","dproperties_claster.php?ids="+ids+"&ontid="+ontid,true);
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


    function writeDataPropertyToDB(){//==============================================mozebi ke treba se smeni mesto
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
        var domen= new String();
        var domen_ida=getSelectedCheckboxValue(document.getElementsByName("exp_cbox"));
        for(var i=0;i<domen_ida.length;i++){
            if(i!=0)
                domen=domen.concat(",");
            domen=domen.concat(domen_ida[i]);
        }
    
        var equiv= new String();
        var equiv_ida=getSelectedCheckboxValue(document.getElementsByName("equiv_cbox"));
        for(var i=0;i<equiv_ida.length;i++){
            if(i!=0)
                equiv=equiv.concat(",");        
            equiv=equiv.concat(equiv_ida[i]);
        }
    
        var disj= new String();
        var disj_ida=getSelectedCheckboxValue(document.getElementsByName("disj_cbox"));
        for(var i=0;i<disj_ida.length;i++){
            if(i!=0)
                disj=disj.concat(",");        
            disj=disj.concat(disj_ida[i]);
        }

        var a=0;
        if(document.getElementById("funkc").checked){
            a+=1;}

        xmlhttp.open("GET","dproperties_claster_write.php?uid="+document.getElementById("userid").value+"&mid="+document.getElementById("moduleid").value+"&ime="+encodeURIComponent(document.getElementById("imeT").value)+"&opis="+encodeURIComponent(document.getElementById("opisT").value)+"&site_ida="+document.getElementById("site_ida").value+"&disjointID="+disj+"&equivalentID="+equiv+"&expressionID="+domen+"&attributes="+a+"&rangID="+document.getElementById("rang").value+"&superpropertyID="+document.getElementById("superproperty").value,true);
        xmlhttp.send();
        refresh();
    }

    function smeni_vrednosti(){
        var novi=document.getElementById("procent").value;
        document.getElementById("funkc").checked=(novi.charAt(0)=='1');
    }

    function refresh()
    {
        document.getElementById("stekk").value="";
        document.getElementById("heading").style.visibility="Visible";
        document.getElementById("prikaz").style.visibility="Visible";
        setTimeout("Prikaz()",200);
    }

    function nazad()
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
    function superproperty_submit(prazno){
        if(prazno.value!="")
            document.getElementById("write_btn").style.visibility="visible";
    }
    function canceled()
    {
        window.location= <?php echo "\"teacherbuilddproperties.php?id=" . $id . "\"" ?>
    }
    function property_select(list){
        document.getElementById("propertyid").value=list.value;
        document.getElementById("next").style.visibility="visible";
    }

    function nazad_spojuvanje()
    {
        window.location= <?php echo "\"teacherbuilddproperties.php?id=" . $id . "\"" ?>
    }

    function confirm(){
        var id=document.getElementById("propertyid").value;
        var id2=document.getElementById("propertyid2").value;
        document.getElementById("stekk").value=id2+" "+id;
        KlasteriraniPSvojstva();
    }
</script>

<b id="heading"> <?php echo get_string('sort', 'ontology'); ?> </b>
<select id="prikaz" onchange="Prikaz();">
    <option value="1"> <?php echo get_string('by_rating', 'ontology'); ?> </option>
    <option value="2"> <?php echo get_string('alphabetical', 'ontology'); ?></option>
</select>

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