<?php
/**
 * The options page functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The debug tab of the options page, display saved data
 *
 * Adds a section to the tab, displaying the data
 *
 * @since		1.0.0
 * @package		recipepress-reloaded
 * @subpackage	recipepress-reloaded/admin
 * @author		Jan Köster <rpr@cbjck.de>
 */
class RPR_Options_Page_Modules_List {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'rpr_options';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'modules';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'modules';
	
   
    /**
     * A list of available modules with their paths
     */
    private $modules = array();
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
        
        /**
         * Can't all this go to update?
         * It'll only change when new modules are being installed => update
         * Probably only possible for the hidden options as chexboxes won't be displayed...
         */
        /**
         * Get a list of all available modules
         */
        $this->get_modules_list();
        
        /* Create the sections for these options */
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug   
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => '<i class="fa fa-puzzle-piece"></i>&nbsp;' . __( 'Available modules', 'recipepress-reloaded' )
            )
        );

         /**
         * Create a checkbox list of all modules
         */
        
        // TODO: Move this to a function!
        if( is_array( $this->modules ) && count( $this->modules ) > 0 ){
            asort( $this->modules );
            foreach ( $this->modules as $module ){
                if( $module['selectable'] == true ){
                    $checkbox = array();
                    $checkbox['type'] = 'checkbox';
                    $checkbox['field_id'] = 'module_' . $module['id'] . '_active';
                    $checkbox['title'] = '<i class="fa ' . $this->modules[$module['id']]['icon'] . '"></i>&nbsp;' . $module['title'];
                    $checkbox['description'] = $this->get_module_description( $module['id'] );
                    $checkbox['tip'] = __( 'Check to activate the module and enable its functionality', 'recipepress-reloaded' );
                } else {
                    $checkbox = array();
                    $checkbox['type'] = 'hidden';
                    $checkbox['field_id'] = 'module_' . $module['id'] . '_active';
                    $checkbox['default'] = true;
                }
                // Add to setting:
                $oFactory->addSettingFields(
                    array( $this->sSectionID ),
                    $checkbox
                );
                // Add a hidden option containing the priority
                $prio = array();
                $prio['type'] = 'hidden';
                $prio['field_id'] = 'module_' . $module['id'] . '_priority';
                $prio['value'] = $module['priority'];
                $oFactory->addSettingFields(
                    array( $this->sSectionID ),
                    $prio
                );
            }
        }
    }
    
    // @todo: Move this to a helper class and use it here and in class-rpr
    /**
     * Create a list of available layouts loacally and globally.
     */
    private function get_modules_list() {
        /**
         * First create a list of all globally available layouts:
         */
	$dirname = WP_PLUGIN_DIR . '/recipepress-reloaded/modules/';
                
        $this->add_module_to_list($dirname);
    }
    
    /**
     * Create a list of available layouts at the dir specified
     * The list will be saved as id=>path to the global layouts array
     * @param type $dirname Path where we should search for layouts
     */
    private function add_module_to_list( $dirname ){
        if ( is_dir( $dirname ) ){
            if ($handle = opendir( $dirname )) {
                // Walk through all folders in that directory:
                while (false !== ($file = readdir($handle))) {
                    if( $file !='.' && $file !='..' && $file != '.svn' ) {
                        if( preg_match( "/plugin/", $dirname ) ){
                            $baseurl = plugins_url() . '/' . 'recipepress-reloaded' . '/modules/' . $file ;
                            $local = false;
                        }
                        $this->modules[$file] = array(
                            'path' => $dirname . $file,
                            'url' => $baseurl,
                            'id'    => $file
                        );
                        $this->get_modules_meta( $dirname, $file );
                    }
                }
            }
        }
    }

    
    private function get_modules_meta( $dirname, $file ) {
        include $dirname . $file . '/module.conf.php';
        
        if( isset( $module_config['title'] ) ){
            $this->modules[$file]['title'] = sanitize_text_field( $module_config['title'] );
        }
        
        if( isset( $module_config['description'] ) ){
            $this->modules[$file]['description'] = sanitize_text_field( $module_config['description'] );
        }
        if( isset( $module_config['version'] ) ){
            $this->modules[$file]['version'] = sanitize_text_field( $module_config['version'] );
        }
        if( isset( $module_config['priority'] ) ){
            $this->modules[$file]['priority'] = sanitize_text_field( $module_config['priority'] );
        } else {
            $this->modules[$file]['priority'] = 0;
        }
        if( isset( $module_config['selectable'] ) ){
            $this->modules[$file]['selectable'] = sanitize_text_field( $module_config['selectable'] );
        } else {
            $this->modules[$file]['selectable'] = true;
        }
        if( isset( $module_config['category'] ) ){
            $this->modules[$file]['category'] = strtolower( sanitize_text_field( $module_config['category'] ) );
            switch ( $module_config['category'] ){
                case 'Metadata':
                    $this->modules[$file]['icon'] = 'fa-tags';
                    break;
                default :
                    $this->modules[$file]['icon'] = 'fa-cogs';
                    break;
            }
        }
        if( isset( $module_config['author'] ) ){
            $this->modules[$file]['author'] = sanitize_text_field( $module_config['author'] );
        }
        if( isset( $module_config['author_mail'] ) ){
            $this->modules[$file]['author_mail'] = sanitize_email( $module_config['author_mail'] );
        }
        if( isset( $module_config['author_url'] ) ){
            $this->modules[$file]['author_url'] = sanitize_text_field( $module_config['author_url'] );
        }
        if( isset( $module_config['doc_url'] ) ){
            $this->modules[$file]['doc_url'] = sanitize_text_field( $module_config['doc_url'] );
        }

//        var_dump( $module_config );
//        
//        // Param parsing inspired by http://stackoverflow.com/questions/11504541/get-comments-in-a-php-file
//	$params=array();
//	$filename = $dirname . $file . '/module.php';
//			
//        // Read comments in the file module.php
//        // f_comment is a filter function currently defined at class-rpr-options-page-appearance_layouts.php
//	$docComments = array_filter(
//		token_get_all( file_get_contents( $filename ) ), 
//			"f_comment"
//	);
//	
//	$fileDocComment = array_shift( $docComments );
//					
//	$regexp = "/.*\:.*\n/";
//	preg_match_all($regexp, $fileDocComment[1], $matches);
//	
//        foreach( $matches[0] as $match ){
//		$param = explode(": ", $match);
//		$params[ trim( $param[0] ) ] = trim( $param[1] );
//	}

//        if( isset( $params['Description'] ) ){
//            $this->modules[$file]['description'] = sanitize_text_field( $params['Description'] );
//        }
//        if( isset( $params['Title'] ) ){
//            $this->modules[$file]['title'] = sanitize_text_field( $params['Title'] );
//        }
//        if( isset( $params['Author'] ) ){
//            $this->modules[$file]['author'] = sanitize_text_field( $params['Author'] );
//        }
//        if( isset( $params['Author Mail'] ) ){
//            $this->modules[$file]['author_mail'] = sanitize_email( $params['Author Mail'] );
//        }
//        if( isset( $params['Author URL'] ) ){
//            $this->modules[$file]['author_url'] = sanitize_text_field( $params['Author URL'] );
//        }
//        if( isset( $params['Version'] ) ){
//            $this->modules[$file]['version'] = sanitize_text_field( $params['Version'] );
//        }
//        if( isset( $params['Documentation URL'] ) ){
//            $this->modules[$file]['doc_url'] = sanitize_text_field( $params['Documentation URL'] );
//        }
//        if( isset( $params['Category'] ) ){
//            $this->modules[$file]['category'] = strtolower( sanitize_text_field( $params['Category'] ) );
//            switch ( $params['Category'] ){
//                case 'Metadata':
//                    $this->modules[$file]['icon'] = 'fa-tags';
//                    break;
//                default :
//                    $this->modules[$file]['icon'] = 'fa-cogs';
//                    break;
//            }
//        }
    }
    
    /**
     * Returns a nicely formatted description text.
     * @param type $module_id
     * @return string
     */
    private function get_module_description( $module_id ) {
        $description = '<span class="layout_description">';
        
        /**
         * Include description if available
         */
        if( isset( $this->modules[$module_id]['description'] ) && ! empty( $this->modules[$module_id]['description'] ) ){
            $description .= $this->modules[$module_id]['description'] . '<br/>';
        }
        
        /**
         * Include link to documentation if available
         */
        if( isset( $this->modules[$module_id]['doc_url'] ) ){
            $description .= '<a href="' . esc_url( $this->modules[$module_id]['doc_url'] ) . '">' . __( 'Documentation', 'recipepress-reloaded' ) . '</a>&nbsp;|&nbsp;';
        }
        /** 
         * Include version information as far as available
         */
        if( isset( $this->modules[$module_id]['version'] ) ){
            //$description .= '<br/>';
            $description .= '<b class="module_desc_label" >' . __( 'Version', 'recipepress-reloaded' ) . ':</b>&nbsp;';
            $description .= '<span>' . $this->modules[$module_id]['version'] . '</span>';
        }
        /**
         * Include author and contact information as far as available
         */
        if( isset( $this->modules[$module_id]['author'] ) ){
            $description .= '&nbsp;|&nbsp;';
            $description .= '<b class="module_desc_label" >' . __( 'Author', 'recipepress-reloaded' ) . ':</b>&nbsp;';
            if( isset( $this->modules[$module_id]['author_mail'] )){
                $description .= '<a href="mailto:' . $this->modules[$module_id]['author_mail'] . '">';
            }
            $description .= $this->modules[$module_id]['author'];
            if( isset( $this->modules[$module_id]['author_mail'] )){
                $description .= '</a>';
            }
        }
        /**
         * Include homepage if available
         */
        if( isset( $this->modules[$module_id]['author_url'] ) ){
            $description .= '&nbsp;(';
            $description .= '<a title="' . __( 'Homepage', 'recipepress-reloaded' ) . '" href="' . esc_url( $this->modules[$module_id]['author_url'] ) . '" target="_blank">' . $this->modules[$module_id]['author_url']  . '</a>';
            $description .=')';
        }
        $description .= '</span>';
                
        return $description;
    }
}
/*
function f_comment( $entry ){
	return $entry[0] == T_COMMENT;
    }
 * 
 */