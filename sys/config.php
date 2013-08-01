<?php
// =============================================================================
// AQUA CMS
// defining globals
// =============================================================================

foreach(array(
    // -------------------------------------------------------------------------
    // MAIN CONFIGURATION
    // -------------------------------------------------------------------------
    // whether the pages are based on dataase or files
    'CONF_DB_BASED'                 =>  true,
    // whether to auto create the database structure in MySQL for the site
    'CONF_DB_AUTO_CREATE'           =>  true,
    // connection data
    'CONF_DB'                       =>  'localhost',
    'CONF_DB_NAME'                  =>  'allatmenhely',
    'CONF_DB_USER'                  =>  'root',
    'CONF_DB_PASSWORD'              =>  '',
    'CONF_DB_PREFIX'                =>  false,
    // these are overwritten by database options when present
    // give a MySQL collation by default
    'CONF_DB_COLLATION'             =>  'utf8_hungarian_ci',
    // set names in MySQL by default
    'CONF_DB_SET_NAMES'             =>  'latin2',
    // basic defaults for the site
    'SITE_NAME'                     =>  'MyNewSite',
    'SITE_TITLE'                    =>  'MyNewSite',
    'SITE_DESCRIPTION'              =>  false,
    'SITE_KEYWORDS'                 =>  false,
    'SITE_URL'                      =>  'http://127.0.0.1',
    
    'SITE_OWNER'                    =>  false,
    'SITE_OWNER_EMAIL'              =>  false,
    'SITE_OWNER_URL'                =>  false,
    'SITE_OWNER_URL_GOOGLE'         =>  false,
    
    'SITE_TITLE_LAYOUT'             =>  '{title} | {mainTitle}',
    'SITE_DESCRIPTION_LAYOUT'       =>  false,
    'SITE_KEYWORDS_LAYOUT'          =>  false,
    
    'SITE_THEME'                    =>  'aqua',
    // -------------------------------------------------------------------------
    // LANGUAGE
    // -------------------------------------------------------------------------
    // basic words and expressions
    'W_YES'                         =>  'yes',
    'W_NO'                          =>  'no',
    'W_USER'                        =>  'user',
    'W_USERNAME'                    =>  'username',
    'W_PASSWORD'                    =>  'password',
    'W_WEBSITE'                     =>  'website',
    'W_MONTHS'                      =>  'month;'.
                                        'january;'.
                                        'february;'.
                                        'march;'.
                                        'june;'.
                                        'july;'.
                                        'august;'.
                                        'september;'.
                                        'october;'.
                                        'november;'.
                                        'december',
    'W_MONTHS_SHORT'                =>  'mn;'.
                                        'jan;'.
                                        'feb;'.
                                        'mar;'.
                                        'jun;'.
                                        'jul;'.
                                        'aug;'.
                                        'sep;'.
                                        'oct;'.
                                        'nov;'.
                                        'dec',
    'W_DAYS'                        =>  'day;'.
                                        'monday;'.
                                        'tuesday;'.
                                        'wednesday;'.
                                        'thursday;'.
                                        'friday;'.
                                        'saturday;'.
                                        'sunday',
    'W_DAYS_SHORT'                  =>  'day;'.
                                        'mon;'.
                                        'tue;'.
                                        'wed;'.
                                        'thu;'.
                                        'fri;'.
                                        'sat;'.
                                        'sun',
    'W_DB'                          =>  'Database',
    'W_DB_ERROR'                    =>  'Database error.',
    'W_DB_ERROR_PRIVILEGE'          =>  'Database privilege error.',
    'W_DB_ERROR_CREATION'           =>  'Database creation error.'
) as $name => $value):
    // if not defined, define the globalse set above
    defined($name) ? NULL : define($name,$value);
endforeach;
?>
