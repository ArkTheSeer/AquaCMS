<?php
// =============================================================================
// AQUA CMS
// database class
// =============================================================================
class Database {
    // declaring properties
    protected $connection;
    // -------------------------------------------------------------------------
    // METHODS
    // -------------------------------------------------------------------------
    // Connecting to Database
    // -------------------------------------------------------------------------
    public function __construct(){
        // connect to database using pre-configured globals
        $this->connection = mysql_connect(CONF_DB,
                                          CONF_DB_USER,
                                          CONF_DB_PASSWORD)
                                or die(W_DB_ERROR);
        // auto-check database environment
        $this->autoCheck();
    }
    // -------------------------------------------------------------------------
    // Auto Check
    // auto-check database environment, auto create
    // -------------------------------------------------------------------------
    protected function autoCheck(){
        // setting variables
        $time = date('Y-m-d H:i:s',time());
        $coll = '';
        // if configured, set MySQL collation
        CONF_DB_COLLATION ? $coll = ' COLLATE '.CONF_DB_COLLATION : NULL;
        // connect to database, if not exists create it
        if(!mysql_select_db(CONF_DB_NAME,$this->connection)):
            mysql_query('CREATE DATABASE IF NOT EXISTS '
                        .CONF_DB_NAME.$coll)
                or die(W_DB_ERROR_PRIVILEGE);
            mysql_select_db(CONF_DB_NAME,$this->connection);
        endif;
        // auto creating tables
        if(CONF_DB_AUTO_CREATE):
            // use prefix if configured
            $pre = CONF_DB_PREFIX;
            // create users
            mysql_query("CREATE TABLE IF NOT EXISTS {$pre}users (
                            id INT(100) NOT NULL AUTO_INCREMENT, 
                            username VARCHAR(100), 
                            password VARCHAR(100), 
                            email VARCHAR(100), 
                            joined DATETIME,
                            PRIMARY KEY (id))".$coll)
                or die(W_DB_ERROR_CREATION);
            mysql_query("INSERT IGNORE INTO {$pre}users (
                            id,
                            username,
                            password,
                            email,
                            joined
                         ) VALUES (
                            1,
                            'admin',
                            '65cb59645b852c2394ba3ff8b295e83c',
                            '".SITE_OWNER_EMAIL."',
                            '{$time}'
                         )")
                or die(W_DB_ERROR_CREATION);
            // create users_meta
            mysql_query("CREATE TABLE IF NOT EXISTS {$pre}users_meta (
                            id INT(100) NOT NULL AUTO_INCREMENT, 
                            user_id INT(100), 
                            label VARCHAR(100), 
                            value TEXT, 
                            updated DATETIME,
                            PRIMARY KEY (id))".$coll)
                or die(W_DB_ERROR_CREATION);
            // create content
            mysql_query("CREATE TABLE IF NOT EXISTS {$pre}content (
                            id INT(100) NOT NULL AUTO_INCREMENT, 
                            URL VARCHAR(100), 
                            title VARCHAR(100), 
                            content TEXT,
                            type VARCHAR(50),
                            created DATETIME,
                            PRIMARY KEY (id))".$coll)
                or die(W_DB_ERROR_CREATION);
            // create welcome page
            if(!mysql_num_rows(
                    mysql_query("SELECT id FROM {$pre}content 
                        WHERE URL='index'"))):
                mysql_query("INSERT IGNORE INTO {$pre}content (
                                id,URL,title,content,type,created
                             ) VALUES (
                                1,'index','Welcome',
                                '<section id=\"page\"><h2>Welcome</h2>".
                                "<p class=\"center\">This is your AquaCMS site</p>',
                                'page','{$time}'
                             )")or die(W_DB_ERROR_CREATION);
            endif;
            // create content_meta
            mysql_query("CREATE TABLE IF NOT EXISTS {$pre}content_meta (
                            id INT(100) NOT NULL AUTO_INCREMENT, 
                            content_id INT(100), 
                            label VARCHAR(100), 
                            value TEXT, 
                            updated DATETIME,
                            PRIMARY KEY (id))".$coll)
                or die(W_DB_ERROR_CREATION);
            // create options
            mysql_query("CREATE TABLE IF NOT EXISTS {$pre}options (
                            id INT(100) NOT NULL AUTO_INCREMENT,
                            name VARCHAR(100),
                            value VARCHAR(100),
                            PRIMARY KEY (id))".$coll)
                or die(W_DB_ERROR_CREATION);
            $allConst = get_defined_constants(true);
            $const = $allConst['user'];
            if(!empty($const)): foreach($const as $key => $value):
                if($key!='CONF_DB_BASED'
                   &&$key!='CONF_DB_AUTO_CREATE'
                   &&$key!='CONF_DB'
                   &&$key!='CONF_DB_NAME'
                   &&$key!='CONF_DB_USER'
                   &&$key!='CONF_DB_PASSWORD'
                   &&$key!='CONF_DB_PREFIX'
                   &&substr($key,0,2)!="W_"):
                    if(!mysql_num_rows(
                        mysql_query("SELECT id FROM {$pre}options WHERE
                                     name='{$key}'"))):
                        mysql_query("INSERT IGNORE INTO {$pre}options
                                     (id,name,value)
                                     VALUES 
                                    (NULL,'{$key}','".constant($key)."')")
                            or die(W_DB_ERROR_CREATION);
                    endif;
                endif;
            endforeach; endif;
        endif;
        // setting MySQL names if configured
        if(CONF_DB_SET_NAMES):
            mysql_query("SET NAMES '".CONF_DB_SET_NAMES."'") 
                or die(W_DB_ERROR_CREATION); 
        endif;
    }
    // -------------------------------------------------------------------------
    // Query
    // query the database and return results
    // -------------------------------------------------------------------------
    public function query($q){
        // setting up returning variable
        $return = array();
        // perform the query
        $result = mysql_query($q);
        if($result):
            // store the number of results
            $return[0] = mysql_num_rows($result);
            // if there are results, collect data
            if($return[0]): 
                while($row=  mysql_fetch_array($result,MYSQLI_ASSOC)):
                    $data = array();
                    foreach ($row as $key => $value):
                        $data[$key] = $value;
                    endforeach;
                    $return[] = $data;
                endwhile;
                // return the results in an array
                return $return;
            else:
                // no results
                return false;
            endif;
        else:
            // no results
            return false;
        endif;
    }
};
?>
