<?php
// =============================================================================
// AQUA CMS
// core functions
// =============================================================================

// -----------------------------------------------------------------------------
// INCLUDE SYSTEM FILES
// at initialization
// -----------------------------------------------------------------------------
function includeSystemFiles(){
    // getting all the files in the 'sys' directory
    $sysFiles = scandir('sys');
    // removing ',', '..', abd the initialization files from the list
    unset(  $sysFiles[0],
            $sysFiles[1],
            $sysFiles[array_search('config.php',$sysFiles)],
            $sysFiles[array_search('initialize.php',$sysFiles)],
            $sysFiles[array_search('functions.php',$sysFiles)]);
    // sorting by ABC
    sort($sysFiles);
    // setting up the 3 main categories of system files
    $scripts    = array();
    $classes    = array();
    $other      = array();
    // ordering the files into the 3 groups according to filenames
    foreach($sysFiles as $filename):
        if(preg_match('{script\.}i',$filename)):
            $scripts[]  = $filename;
        elseif(preg_match('{class\.}i',$filename)):
            $classes[]  = $filename;
        else:
            $other[]    = $filename;
        endif;
    endforeach;
    // including system files in groupped order
    $includes = array($scripts,$classes,$other);
    foreach($includes as $include):
        if(!empty($include)): foreach($include as $filename):
            include_once "sys/{$filename}";
        endforeach; endif;
    endforeach;
};

// -----------------------------------------------------------------------------
// START HTML
// start rendering HTML
// -----------------------------------------------------------------------------
function startHTML(){
    // initializing the Content class
    $GLOBALS['p'] = new Content;
    // inserting basic HTML
    $GLOBALS['p']->render();
};

// -----------------------------------------------------------------------------
// INPUT
// cleaning user input from potential hacks/harmful elements
// -----------------------------------------------------------------------------
function input($input){
    // removing anything other than alphanumeric chars, dashes and spaces
    $input = preg_replace("/[^A-Za-z0-9\- ]/","",$input);
    // removing html
    $input = htmlspecialchars($input,ENT_NOQUOTES|'ENT_HTML5');
    // removing tags
    $input = strip_tags($input);
    // preparing input for MySQL insertion
    if(class_exists('Database')):
        $d = new Database;
        $input = mysql_real_escape_string($input);
    else:
        $input = mysql_escape_string($input);
    endif;
    // returning the cleaned input 
    return $input;
};
?>
