<?php
/*
Plugin Name:    My Insert JavaScript & CSS
Plugin URI:     http://www.nutt.net/tag/insert-javascript-css/
Description:    Adds a field to the post / page edit screens to allow you to insert custom JavaScript and CSS for just that post or page.
Version:        0.2
Author:         Ryan Nutt, jenkin
Author URI: 	http://www.nutt.net
License:        GPLv2
*/

/* Settings */

/**
 * This is the WordPress capability that is required to be able to insert JavaScript
 * or CSS in posts and pages. 
 */
define('IJSC_CAPABILITY_REQUIRED', 'upload_files');

/* End of settings - you shouldn't need to edit anything below this point */

$ijsc = new InsertJavaScriptCSS();
class InsertJavaScriptCSS {
    
    public function __construct() {
        
        add_action('admin_head', array($this, 'admin_head'));
        wp_register_script('ijsc', plugins_url('/js/ijsc.js', __FILE__), array('jquery')); 
        wp_register_style('ijsc', plugins_url('/css/ijsc.css', __FILE__)); 
        add_action('admin_print_styles', array($this, 'print_styles')); 
        
        add_action('post_submitbox_misc_actions', array($this, 'post_fields'));
        add_action('save_post', array($this, 'save_post'));
        add_action('the_posts', array($this, 'the_posts')); 
    }
    
    public function the_posts($posts) {
        add_action('wp_head', array($this, 'add_to_head')); 
        return $posts; 
    }
    
    public function add_to_head() {
        if(is_single()) {
            global $post;
            $vals = get_post_meta($post->ID);

            if (isset($vals['_ijsc-ctag'][0])) {
                echo $this->formatCtag($vals['_ijsc-ctag'][0]);
            }
            if (isset($vals['_ijsc-datalink'][0])) {
                echo $this->formatData($vals['_ijsc-datalink'][0]);
            }
            if (isset($vals['_ijsc-rawdata'][0])) {
                echo $this->formatRawData($vals['_ijsc-rawdata'][0]);
            }
            if (isset($vals['_ijsc-jslib'][0])) {
                echo $this->formatJSlib($vals['_ijsc-jslib'][0]); 
            }
            if (isset($vals['_ijsc-js'][0])) {
                echo $this->formatJS($vals['_ijsc-js'][0]); 
            }
            if (isset($vals['_ijsc-csslink'][0])) {
                echo $this->formatCSSlinks($vals['_ijsc-csslink'][0]);
            }
            if (isset($vals['_ijsc-css'][0])) {
                echo $this->formatCSS($vals['_ijsc-css'][0]);
            }
            
        }
        
    }
    
    private function formatCtag($ctag) {
        echo "\n".trim($ctag)."\n";
    }
    
    private function formatData($datalinks) {

        $urls = explode("\n",trim($datalinks));

        foreach($urls as $url) {
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            echo '<link rel="data" type="application/'.$ext.'" href="' . trim($url) . '">'."\n";
        }
    }
    
    private function formatRawData($data) {
        $data = trim($data);
        
        if (!preg_match('/^<script(.*)<\/script>$/s', $data)) {
            $data = '<script type="text/javascript">'."\n".$data."\n</script>"; 
        }
        
        echo "\n".$data."\n"; 
    }
    
    private function formatJS($js) {
        $js = trim($js);
        
        if (!preg_match('/^<script(.*)<\/script>$/s', $js)) {
            $js = '<script type="text/javascript">'."\n".$js."\n</script>"; 
        }
        
        echo "\n".$js."\n"; 
    }
    
    private function formatJSlib($jslib) {

        $urls = explode("\n",trim($jslib));

        foreach($urls as $url) {
            echo '<script type="text/javascript" src="' . trim($url) . '"></script>'."\n";
        }
    }
    
    private function formatCSSlinks($csslinks) {

        $urls = explode("\n",trim($csslinks));

        foreach($urls as $url) {
            echo '<link rel="stylesheet" type="text/css" href="' . trim($url) . '">'."\n";
        }
    }
    
    private function formatCSS($css) {
        $css = trim($css);
        
        if (!preg_match('/^<style(.*)<\/style>$/s', $css)) {
            $css = '<style type="text/css">'."\n".$css."\n</style>"; 
        }
        
        echo "\n".$css."\n";
    }
    
    public function save_post($postID) { 
        if (!current_user_can(IJSC_CAPABILITY_REQUIRED)) {
            return;
        }
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        
        if (isset($_POST['ijsc-field-ctag']) && trim($_POST['ijsc-field-ctag']) != '') {
            update_post_meta($postID, '_ijsc-ctag', $_POST['ijsc-field-ctag']); 
        }
        else {
            delete_post_meta($postID, '_ijsc-ctag'); 
        }
        if (isset($_POST['ijsc-field-datalink']) && trim($_POST['ijsc-field-datalink']) != '') {
            update_post_meta($postID, '_ijsc-datalink', $_POST['ijsc-field-datalink']); 
        }
        else {
            delete_post_meta($postID, '_ijsc-datalink'); 
        }
        if (isset($_POST['ijsc-field-rawdata']) && trim($_POST['ijsc-field-rawdata']) != '') {
            update_post_meta($postID, '_ijsc-rawdata', $_POST['ijsc-field-rawdata']); 
        }
        else {
            delete_post_meta($postID, '_ijsc-rawdata'); 
        }
        if (isset($_POST['ijsc-field-jslib']) && trim($_POST['ijsc-field-jslib']) != '') {
            update_post_meta($postID, '_ijsc-jslib', $_POST['ijsc-field-jslib']);
        }
        else {
            delete_post_meta($postID, '_ijsc-jslib'); 
        }
        if (isset($_POST['ijsc-field-js']) && trim($_POST['ijsc-field-js']) != '') {
            update_post_meta($postID, '_ijsc-js', $_POST['ijsc-field-js']);
        }
        else {
            delete_post_meta($postID, '_ijsc-js'); 
        }
        if (isset($_POST['ijsc-field-csslink']) && trim($_POST['ijsc-field-csslink']) != '') {
            update_post_meta($postID, '_ijsc-csslink', $_POST['ijsc-field-csslink']);
        }
        else {
            delete_post_meta($postID, '_ijsc-csslink'); 
        }
        if (isset($_POST['ijsc-field-css']) && trim($_POST['ijsc-field-css']) != '') {
            update_post_meta($postID, '_ijsc-css', $_POST['ijsc-field-css']); 
        }
        else {
            delete_post_meta($postID, '_ijsc-css'); 
        }
    }
    
    /**
     * Add hidden input fields to the page. They're going in the submit meta
     * box because it has an easy action to hook on to.
     */
    public function post_fields($p) {
        global $post; 
        
        wp_enqueue_script('ijsc');
        
        echo '<textarea style="display:none;" name="ijsc-field-ctag" id="ijsc-field-ctag">'.htmlentities(get_post_meta($post->ID, '_ijsc-ctag', true)).'</textarea>'; 
        echo '<textarea style="display:none;" name="ijsc-field-datalink" id="ijsc-field-datalink">'.htmlentities(get_post_meta($post->ID, '_ijsc-datalink', true)).'</textarea>'; 
        echo '<textarea style="display:none;" name="ijsc-field-rawdata" id="ijsc-field-rawdata">'.htmlentities(get_post_meta($post->ID, '_ijsc-rawdata', true)).'</textarea>'; 
        echo '<textarea style="display:none;" name="ijsc-field-jslib" id="ijsc-field-jslib">'.htmlentities(get_post_meta($post->ID, '_ijsc-jslib', true)).'</textarea>';
        echo '<textarea style="display:none;" name="ijsc-field-js" id="ijsc-field-js">'.htmlentities(get_post_meta($post->ID, '_ijsc-js', true)).'</textarea>';
        echo '<textarea style="display:none;" name="ijsc-field-csslink" id="ijsc-field-csslink">'.htmlentities(get_post_meta($post->ID, '_ijsc-csslink', true)).'</textarea>';
        echo '<textarea style="display:none;" name="ijsc-field-css" id="ijsc-field-css">'.htmlentities(get_post_meta($post->ID, '_ijsc-css', true)).'</textarea>'; 
        
        echo '<script type="text/javascript">ijsc.initPostPage();</script>'; 
    }    
    
    
    public function admin_head() {
        if (current_user_can(IJSC_CAPABILITY_REQUIRED)) {
             add_action('media_buttons_context', array($this, 'media_buttons'));
             wp_enqueue_script('ijsc'); 
             
        }
    }
    
    public function print_styles() {
        wp_enqueue_script('ijsc'); 
    }
    
    public function media_buttons($cnt) {
        if (!current_user_can(IJSC_CAPABILITY_REQUIRED)) {
            /* User doesn't have capability, just return what's already there */
            return $cnt; 
        }
        
        global $post_ID, $temp_ID;
        
        $new = '<a href="'.plugins_url('/ijsc-frame.php', __FILE__).'?postID='.$post_ID.'&TB_iframe=true" class="thickbox" id="ijsc_link" title="'.__('Insert JavaScript / CSS', 'ijsc').'">';
        $new .= '<img title="'.__('Insert JavaScript / CSS', 'ijsc').'" alt="'.__('Insert JavaScript / CSS', 'ijsc').'" src="'.plugins_url('/img/add_icon_bw.png', __FILE__).'" data-color="'.plugins_url('/img/add_icon.png', __FILE__).'" data-bw="'.plugins_url('/img/add_icon_bw.png', __FILE__).'" data-edit="'.__('Edit existing JavaScript / CSS', 'ijsc').'" data-insert="'.__('Insert JavaScript / CSS', 'ijsc').'" id="ijsc-icon">';
        $new .= '</a>'; 
        
        return $cnt . $new; 
        
    }
    
}

?>
