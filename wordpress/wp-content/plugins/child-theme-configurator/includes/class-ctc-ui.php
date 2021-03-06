<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
/*
    Class: Child_Theme_Configurator_UI
    Plugin URI: http://www.childthemeconfigurator.com/
    Description: Handles the plugin User Interface
    Version: 1.7.2.1
    Author: Lilaea Media
    Author URI: http://www.lilaeamedia.com/
    Text Domain: chld_thm_cfg
    Domain Path: /lang
    License: GPLv2
    Copyright (C) 2014-2015 Lilaea Media
*/
class ChildThemeConfiguratorUI {
    // helper function to globalize ctc object
    
    function ctc() {
        return ChildThemeConfigurator::ctc();
    }

    function render() {
        $css        = $this->ctc()->css;
        $themes     = $this->ctc()->themes;
        $child      = $css->get_prop( 'child' );
        $hidechild  = ( count( $themes[ 'child' ] ) ? '' : 'style="display:none"' );
        $enqueueset = ( isset( $css->enqueue ) && $child );
        $mustimport = $this->parent_stylesheet_check();
        $imports    = $css->get_prop( 'imports' );
        $id         = 0;
        $this->ctc()->fs_method = get_filesystem_method();
        add_thickbox();
        add_filter( 'chld_thm_cfg_files_tab_filter',    array( $this, 'render_files_tab_options' ) );
        add_action( 'chld_thm_cfg_tabs',                array( $this, 'render_addl_tabs' ), 10, 4 );
        add_action( 'chld_thm_cfg_panels',              array( $this, 'render_addl_panels' ), 10, 4 );
        add_action( 'chld_thm_cfg_related_links',       array( $this, 'lilaea_plug' ) );
        if ( $this->ctc()->is_debug ):
            $this->ctc()->debug( 'adding new debug action...', __FUNCTION__ );
            add_action( 'chld_thm_cfg_print_debug', array( $this->ctc(), 'print_debug' ) );
        endif;
        include ( CHLD_THM_CFG_DIR . '/includes/forms/main.php' ); 
    } 

    function parent_stylesheet_check() {
        $file  = trailingslashit( get_theme_root() ) . trailingslashit( $this->ctc()->get_current_parent() ) . 'header.php';
        $regex = '/<link[^>]+?stylesheet_ur[li]/is';
        if ( file_exists( $file ) ):
            $contents = file_get_contents( $file );
            if ( preg_match( $regex, $contents ) ) return TRUE;
        endif;
        return FALSE;
    }
   
    function render_theme_menu( $template = 'child', $selected = NULL ) {
         ?>
        <select class="ctc-select" id="ctc_theme_<?php echo $template; ?>" name="ctc_theme_<?php echo $template; ?>" style="visibility:hidden" <?php echo $this->ctc()->is_theme() ? '' : ' disabled '; ?> ><?php
            foreach ( $this->ctc()->themes[ $template ] as $slug => $theme )
                echo '<option value="' . $slug . '"' . ( $slug == $selected ? ' selected' : '' ) . '>' 
                    . esc_attr( $theme[ 'Name' ] ) . '</option>' . LF; 
        ?>
        </select>
        <div style="display:none">
        <?php 
        foreach ( $this->ctc()->themes[ $template ] as $slug => $theme )
            include ( CHLD_THM_CFG_DIR . '/includes/forms/themepreview.php' ); ?>
        </div>
        <?php
    }
        
    function render_file_form( $template = 'parnt' ) {
        global $wp_filesystem; 
        if ( $theme = $this->ctc()->css->get_prop( $template ) ):
            $themeroot  = trailingslashit( get_theme_root() ) . trailingslashit( $theme );
            $files      = $this->ctc()->get_files( $theme );
            $counter    = 0;
            sort( $files );
            ob_start();
            foreach ( $files as $file ):
                $templatefile = preg_replace( '%\.php$%', '', $file );
                $excludes = implode( "|", ( array ) apply_filters( 'chld_thm_cfg_template_excludes', $this->ctc()->excludes ) );
                if ( 'parnt' == $template && ( preg_match( '%^(' . $excludes . ' )\w*\/%',$templatefile ) 
                    || 'functions' == basename( $templatefile ) ) ) continue; 
                include ( CHLD_THM_CFG_DIR . '/includes/forms/file.php' );            
            endforeach;
            if ( 'child' == $template && ( $backups = $this->ctc()->get_files( $theme, 'backup,pluginbackup' ) ) ):
                foreach ( $backups as $backup => $label ):
                    $templatefile = preg_replace( '%\.css$%', '', $backup );
                    include ( CHLD_THM_CFG_DIR . '/includes/forms/backup.php' );            
                endforeach;
            endif;
            $inputs = ob_get_contents();
            ob_end_clean();
            if ( $counter ):
                include ( CHLD_THM_CFG_DIR . '/includes/forms/fileform.php' );            
            endif;
        endif;
    }
    
    function render_image_form() {
         
        if ( $theme = $this->ctc()->css->get_prop( 'child' ) ):
            $themeuri   = trailingslashit( get_theme_root_uri() ) . trailingslashit( $theme );
            $files = $this->ctc()->get_files( $theme, 'img' );
            
            $counter = 0;
            sort( $files );
            ob_start();
            foreach ( $files as $file ): 
                $templatefile = preg_replace( '/^images\//', '', $file );
                include( CHLD_THM_CFG_DIR . '/includes/forms/image.php' );             
            endforeach;
            $inputs = ob_get_contents();
            ob_end_clean();
            if ( $counter ) include( CHLD_THM_CFG_DIR . '/includes/forms/images.php' );
        endif;
    }
    
    function get_theme_screenshot() {
        
        foreach ( array_keys( $this->ctc()->imgmimes ) as $extreg ): 
            foreach ( explode( '|', $extreg ) as $ext ):
                if ( $screenshot = $this->ctc()->css->is_file_ok( $this->ctc()->css->get_child_target( 'screenshot.' . $ext ) ) ):
                    $screenshot = trailingslashit( get_theme_root_uri() ) . $this->ctc()->theme_basename( '', $screenshot );
                    return $screenshot . '?' . time();
                endif;
            endforeach; 
        endforeach;
        return FALSE;
    }
    
    function settings_errors() {
        
        if ( count( $this->ctc()->errors ) ):
            echo '<div class="error"><ul>' . LF;
            foreach ( $this->ctc()->errors as $err ):
                echo '<li>' . $err . '</li>' . LF;
            endforeach;
            echo '</ul></div>' . LF;
        elseif ( isset( $_GET[ 'updated' ] ) ):
            echo '<div class="updated">' . LF;
            if ( 8 == $_GET[ 'updated' ] ):
                echo '<p>' . __( 'Child Theme files modified successfully.', 'chld_thm_cfg' ) . '</p>' . LF;
            else:
                $child_theme = wp_get_theme( $this->ctc()->css->get_prop( 'child' ) );
                echo '<p>' . apply_filters( 'chld_thm_cfg_update_msg', sprintf( __( 'Child Theme <strong>%s</strong> has been generated successfully.
                ', 'chld_thm_cfg' ), $child_theme->Name ), $this->ctc() ) . LF;
                if ( $this->ctc()->is_theme() ):
                echo '<strong>' . __( 'IMPORTANT:', 'chld_thm_cfg' ) . LF;
                if ( is_multisite() && !$child_theme->is_allowed() ): 
                    echo 'You must <a href="' . network_admin_url( '/themes.php' ) . '" title="' . __( 'Go to Themes', 'chld_thm_cfg' ) . '" class="ctc-live-preview">' . __( 'Network enable', 'chld_thm_cfg' ) . '</a> ' . __( 'your child theme.', 'chld_thm_cfg' );
                else: 
                    echo '<a href="' . admin_url( '/customize.php?theme=' . $this->ctc()->css->get_prop( 'child' ) ) . '" title="' . __( 'Live Preview', 'chld_thm_cfg' ) . '" class="ctc-live-preview">' . __( 'Test your child theme', 'chld_thm_cfg' ) . '</a> ' . __( 'before activating.', 'chld_thm_cfg' );
                endif;
                echo '</strong></p>' . LF;
                endif;
             endif;
            echo '</div>' . LF;
        endif;
    }
    
    function render_help_content() {
	    global $wp_version;
	    if ( version_compare( $wp_version, '3.3' ) >= 0 ) {
	
		    $screen = get_current_screen();
                
            // load help content via output buffer so we can use plain html for updates
            // then use regex to parse for help tab parameter values
            
            $regex_sidebar = '/' . preg_quote( '<!-- BEGIN sidebar -->' ) . '(.*?)' . preg_quote( '<!-- END sidebar -->' ) . '/s';
            $regex_tab = '/' . preg_quote( '<!-- BEGIN tab -->' ) . '\s*<h\d id="(.*?)">(.*?)<\/h\d>(.*?)' . preg_quote( '<!-- END tab -->' ) . '/s';
            ob_start();
            // stub for multiple languages future release
            include( CHLD_THM_CFG_DIR . '/includes/help/help_en_US.php' );
            $help_raw = ob_get_contents();
            ob_end_clean();
            // parse raw html for tokens
            preg_match( $regex_sidebar, $help_raw, $sidebar );
            preg_match_all( $regex_tab, $help_raw, $tabs );

    		// Add help tabs
            if ( isset( $tabs[ 1 ] ) ):
                while( count( $tabs[ 1 ] ) ):
                    $id         = array_shift( $tabs[ 1 ] );
                    $title      = array_shift( $tabs[ 2 ] );
                    $content    = array_shift( $tabs[ 3 ] );
	    	        $screen->add_help_tab( array(
	    	    	    'id'        => $id,
    		    	    'title'     => $title,
	    		        'content'   => $content, 
                    ) );
                endwhile;
            endif;
            if ( isset( $sidebar[ 1 ] ) )
                $screen->set_help_sidebar( $sidebar[ 1 ] );

        }
    }
    
    function render_addl_tabs( $ctc, $active_tab = NULL, $hidechild = '' ) {
        include ( CHLD_THM_CFG_DIR . '/includes/forms/addl_tabs.php' );            
    }

    function render_addl_panels( $ctc, $active_tab = NULL, $hidechild = '' ) {
        include ( CHLD_THM_CFG_DIR . '/includes/forms/addl_panels.php' );            
    }

    function lilaea_plug() {
        include ( CHLD_THM_CFG_DIR . '/includes/forms/related.php' );
    }
    
    function render_files_tab_options( $output ) {
        $regex = '%<div class="ctc\-input\-cell clear">.*?(</form>).*%s';
        $output = preg_replace( $regex, "$1", $output );
        return $output;
    }
}
?>
