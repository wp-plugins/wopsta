<?php 
include_once ('script_component_factory.php');

new script_component_factory();

if (isset($_POST['operation'])) {
    $newobj = script_component_factory::getNewComponent('jsComponents.xml');
    
    $newobj->setId($_POST['id']);
    $newobj->setName($_POST['componentname']);
    $newobj->setIntmarkup($_POST['intmarkup']);
    $newobj->setFilename($_POST['filename']);
    $newobj->setDashboard($_POST['dashboard']);
    $newobj->setIntjs($_POST['intcode']);
    $ary = explode(';', $_POST['dependicies_filename']);
    $nameary = explode(';', $_POST['dependicies_filename']);
    
    $newarray = array();
    
    if (count($ary) == count($nameary)) {
        for ($i = 0; $i < count($ary); $i++) {
            array_push($newarray, array('filename'=>$ary[$i], 'name'=>$nameary[$i]));
            
        }
        
        $newobj->setDependicies($newarray);
    }
	else 
	{
		echo error;
	}

    
    echo $newobj;
    $newobj->save();
}

?>
<h1>Form</h1>
<form action="form.php" method="post">
    <input type="hidden" name="operation" value="new" />
    <fieldset>
        <legend>
            Add new Component
        </legend>
        <p>
            <label>
                id
            </label>
            <br/>
            <input type="input" name="id" />
        </p>
        <p>
            <label>
                Dashboard
            </label>
            <br/>
            <select name="dashboard">
                <option value="true">True</option>
                <option value="false">False</option>
            </select>
        </p>
        <p>
            <label>
                Component Name
            </label>
            <br/>
            <input type="textarea" name="componentname" />
        </p>
        <p>
            <label>
                filename
            </label>
            <br/>
            <input type="input" name="filename" />
        </p>
        <p>
            <label>
                Init Code
            </label>
            <br/>
            <textarea type="text" name="intcode" /> </textarea>
        </p>
        <p>
            <label>
                Init Markup
            </label>
            <br/>
            <textarea type="text" name="intmarkup" /> </textarea>
        </p>
        <p>
            <label>
                Dependicies :- escape each file with ';' char
            </label>
            <br/>
            <textarea type="text" rows="5" name="dependicies_filename"> </textarea>
        </p>
        <p>
            <label>
                Dependicies :- Full name escape each file with ';' char 
            </label>
            <br/>
            <textarea type="text" rows="5" name="dependicies_name"> </textarea>
        </p>
        <p>
            <input type="submit" value="Submit" />
        </p>
    </fieldset>
</form>