<?php
// =============================================================================
// AQUA CMS
// conten class
// =============================================================================
class Content {
    // declaring main properties
    protected $id;
    public $URL;
    public $title;
    public $content;
    public $meta;
    protected $type;    
    // -------------------------------------------------------------------------
    // METHODS
    // -------------------------------------------------------------------------
    // Initializing
    // -------------------------------------------------------------------------
    public function __construct() {
        // setting the current URL
        if(isset($_GET['p'])):
            $this->URL = input($_GET['p']);
        else:
            $this->URL = 'index';
        endif;
        // getting content data
        $this->getData();
    }
    // -------------------------------------------------------------------------
    // Get Data
    // collecting data
    // -------------------------------------------------------------------------
    protected function getData() {
        // check if data has been already collected
        if($this->type):
            // data already collected
            return true;
        else:
            // if the site is database driven check db, if not check files
            if(CONF_DB_BASED):
                // setting database prefix
                $pre = CONF_DB_PREFIX;
                // opening db connection
                $db = new Database();
                // searching for entry according to URL
                $q = $db->query("SELECT * FROM {$pre}content
                                 WHERE URL='{$this->URL}'
                                 LIMIT 1");
                // if entry exists collect data
                if(is_array($q)):
                    // getting rid of numrows
                    $q = $q[1];
                    // getting main data
                    $this->id = $q['id'];
                    $this->title = $q['title'];
                    $this->content = $q['content'];
                    $this->type = $q['type'];
                    $this->meta['created'] = $q['created'];
                    // collect metadata for content
                    $m = $db->query("SELECT * FROM {$pre}content_meta
                                     WHERE content_id = {$this->id}");
                    if(is_array($m)): foreach($m as $key => $value):
                        if($key):
                            $this->meta[$value['label']] = $value['content'];
                        endif;
                    endforeach; endif;
                    // data collection successful
                    return true;
                endif;
            else:
                // if meta file eyists
                if(file_exists('page/_meta.php')):
                    // include meta file
                    include_once('page/_meta.php');
                    // check for metadata
                    if($GLOBALS['allMeta']):
                        // search URL in metadata
                        $meta = $GLOBALS['allMeta'][$this->URL];
                        // if entry exist collect data
                        if($meta):
                            $this->title = $meta['title'];
                            $this->content = $meta['content'];
                            $this->type = $meta['type'];
                            $this->meta = $meta['meta'];
                            $this->meta['fileBased'] = true;
                            // data collection successful
                            return true;
                        endif;
                    endif;
                endif;
            endif;
            // no data found
            return false;
        endif;
    }
    // -------------------------------------------------------------------------
    // Get
    // magic get method, mainly used for layout properties (views)
    // -------------------------------------------------------------------------
    public function __get($name) {
        // setting layout properties
        if($name=='Title'&&$this->title&&!$this->meta['dontshowTitle']):
            // possible additional variable
            $add = "";
            // the page title
            if($this->meta['subtitle']&&!$this->meta['dontshowSubtitle']):
                $add = " <small>{$this->meta['subtitle']}</small>";
            endif;
            return "<h2>{$this->title}</h2>{$add}";
        elseif($name=='HeaderTitle'):
            // the page title in the header
            if(SITE_TITLE_LAYOUT&&SITE_TITLE):
                $t = str_replace('{mainTitle}',SITE_TITLE,
                        str_replace('{title}',$this->title,
                            SITE_TITLE_LAYOUT));
            else:
                $t = $this->title;
            endif;
            return $t;
        elseif($name=='HeaderDescription'):
            // the page description in the header
            if(SITE_DESCRIPTION_LAYOUT&&SITE_DESCRIPTION):
                $t = str_replace('{mainDescription}',SITE_DESCRIPTION,
                        str_replace('{description}',$this->meta['description'],
                            SITE_DESCRIPTION_LAYOUT));
            elseif($this->meta['description']):
                $t = $this->meta['description'];
            endif;
            return $t;
        elseif($name=='HeaderKeywords'):
            // the page keywords in the header
            if(SITE_KEYWORDS_LAYOUT&&SITE_KEYWORDS):
                $t = str_replace('{mainKeywords}',SITE_KEYWORDS,
                        str_replace('{keywords}',$this->meta['keywords'],
                            SITE_KEYWORDS_LAYOUT));
            elseif($this->meta['keywords']):
                $t = $this->meta['keywords'];
            endif;
            return $t;
        elseif($name=='Description'&&$this->meta['description']):
            // description
            return $this->meta['description'];
        elseif($name=='Keywords'&&$this->meta['keywords']):
            // keywords
            return $this->meta['keywords'];
        elseif($name=='Header'):
            // setting the return variable
            $r = "";
            // current uri
            $uri = $_SERVER['REQUEST_URI'];
            // complete headers
            // title
            $r .= "<title>{$this->HeaderTitle}</title>\n".
                  "<meta property=\"og:title\" ".
                    "content=\"{$this->HeaderTitle}\">\n";
            if($this->HeaderDescription):
                $r .= "<meta property=\"description\" ".
                        "content=\"{$this->HeaderDescription}\">\n".
                      "<meta property=\"og:description\" ".
                        "content=\"{$this->HeaderDescription}\">\n";
            endif;
            // site name
            if(SITE_NAME):
                $r .= "<meta property=\"og:site_name\" ".
                        "content=\"".SITE_NAME."\">\n";
            endif;
            // site url
            if(SITE_URL):
                $r .= "<meta property=\"og:site_url\" ".
                        "content=\"".SITE_URL."\">\n".
                      "<meta property=\"og:url\" ".
                        "content=\"".SITE_URL.$uri."\">\n".
                      "<link rel=\"canonical\" ".
                        "href=\"".SITE_URL.$uri."\">\n";
            endif;
            // author
            if($this->meta['author']):
                $r .= "<meta name=\"author\" ".
                        "content=\"{$this->meta['author']}\">\n".
                      "<meta name=\"article:author\" ".
                        "content=\"{$this->meta['author']}\">\n";
            elseif(SITE_OWNER):
                $r .= "<meta name=\"author\" ".
                        "content=\"".SITE_OWNER."\">\n";
                if($this->meta['category']):
                    $r .= "<meta name=\"article:author\" ".
                            "content=\"".SITE_OWNER."\">\n";
                endif;
                $r .= "<meta name=\"copyright\" ".
                        "content=\"".SITE_OWNER."\">\n";
            endif;
            // google plus link
            if(SITE_OWNER_URL_GOOGLE):
                $r .= "<link rel=\"author\" ".
                        "href=\"".SITE_OWNER_URL_GOOGLE."\">\n";
            endif;
            // date
            if($this->meta['modifiedDate']):
                $r .= "<meta property=\"published_time\" ".
                        "content=\"{$this->meta['modifiedDate']}\">\n";
            elseif($this->meta['publishDate']):
                $r .= "<meta property=\"published_time\" ".
                        "content=\"{$this->meta['publishDate']}\">\n";
            endif;
            // category
            if($this->meta['category']):
                $r .= "<meta property=\"og:type\" content=\"article\">\n".                
                      "<meta property=\"article:section\" ".
                        "content=\"{$this->meta['category']}\">\n";
            else:
                $r .= "<meta property=\"og:type\" content=\"page\">\n";
            endif;
            // keywords and tags
            if($this->HeaderKeywords):
                $r .= "<meta name=\"keywords\" ".
                        "content=\"{$this->HeaderKeywords}\">\n";
                $exp = explode(',',$this->HeaderKeywords);
                if(is_array($exp)): foreach($exp as $value):
                    $r .= "<meta property=\"article:tag\" ".
                            "content=\"{$value}\">\n";
                endforeach; endif;
            endif;
            // images
            if($this->meta['cover']&&SITE_URL):
                $r .= "<meta property=\"og:image\" ".
                        "content=\"".SITE_URL."/{$this->meta['cover']}\">\n".
                      "<link rel=\"image_src\" ".
                        "href=\"".SITE_URL."/{$this->meta['cover']}\">\n";
            endif;
            if($this->meta['thumb']&&SITE_URL):
                foreach($this->meta['thumb'] as $value):
                    $r .= "<meta property=\"og:image\" ".
                            "content=\"".SITE_URL."/{$value}\">\n".
                          "<link rel=\"image_src\" ".
                            "href=\"".SITE_URL."/{$value}\">\n";
                endforeach;
            endif;
            // returning the whole header
            if($r):
                return $r;
            endif;
            return false;
        elseif($name=='Content'):
            if($this->meta['fileBased']):
                include_once "page/{$this->content}";
            else:
                $content = $this->content;
                if(strstr($this->content,'{{INCLUDE=')):
                    $content = explode('{{INCLUDE=',$this->content);
                    foreach($content as $key => $value):
                        if($key%2==0):
                            $value = explode('}}',$value);
                            include_once "{$value[0]}";
                            $e = $value[1];
                        else:
                            $e = $value;
                        endif;
                        echo $e;
                    endforeach;
                else:
                    echo $content;
                endif;
            endif;
        elseif($name=='FullContent'):
            echo $this->Title;
            $this->Content;
        else:
            return false;
        endif;
    }
    // -------------------------------------------------------------------------
    // Render
    // render page with layout
    // -------------------------------------------------------------------------
    public function render(){
        // theme folder
        $theme = 'theme/';
        SITE_THEME ? $theme .= SITE_THEME : $theme .= 'aqua';
        $theme .= '/page.php';
        // if the content type is page, look for theme
        if(!$this->getData()):
            $layout404 = str_replace('page','404',$theme);
            if(file_exists($layout404)):
                include_once $layout404;
            else:
                echo "<!DOCTYPE html>\n<html>\n<head>\n<title>";
                echo SITE_NAME ? "Error404 | ".SITE_NAME : "Error404";
                echo "</title>\n".
                     "<meta name=\"HandheldFriendly\" content=\"true\">\n".
                     "<meta name=\"MobileOptimized\" content=\"width\">\n".
                     "<meta name=\"viewport\" content=\"width=device-width, ".
                     "initial-scale=1.0, maximum-scale=1.0, ".
                     "user-scalable=no\">\n".
                     "<style>\n* { margin:0px; padding:0px }\n".
                     "html, body { height:100% }\n".
                     "h1, p { text-align:center; color:#666 }\n".
                     "strong { color:#000 }\n".
                     "div { position:absolute; background:#eee; ".
                     "width:320px; height:100px; top:50%; left:50%; ".
                     "margin:-80px 0 0 -160px; padding:30px 0; ".
                     "box-shadow:2px 2px 5px #aaa }\n".
                     "</style>\n".
                     "</head>\n<body>\n<div>\n".
                     "<h1>error<strong>404</strong></h1>\n".
                     "<p>The page you are looking for doesn't exist.</p>\n".
                     "</div></body></html>";
            endif;
        elseif($this->type=='page'):
            // setting theme
            if(file_exists($theme)):
                // check for different layout
                if($this->meta['layout']):
                    $layout = str_replace('page',$this->meta['layout'],$theme);
                    if(file_exists($layout)):
                        $theme = $layout;
                    endif;
                endif;
                // include theme file
                include_once $theme;
            else:
                // if theme not found display content
                $this->FullContent;
            endif;
        else:
            // if not a page display content
            $this->FullContent;
        endif;
    }
};
?>
