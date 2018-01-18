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
 * @since		0.8.0
 * @package		recipepress-reloaded
 * @subpackage	recipepress-reloaded/admin
 * @author		Jan Köster <rpr@cbjck.de>
 */
class RPR_Options_Page_Appearance_Layout {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'rpr_options';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'appearance';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'layout';
	
   
    /**
     * A list of available layouts with their paths
     */
    private $layouts = array();
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        /**
         * Get a list of all available layouts
         */
        $this->get_layouts_list();
        
        
        /* Create the sections for these options */
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug   
            array(
                'section_id'    => 'layout_general',
                'tab_slug'      => $this->sTabSlug,
                'title'         => '<i class="fa fa-columns"></i>&nbsp;' . __( 'Layout', 'recipepress-reloaded' )
            ),
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Available Layouts', 'recipepress-reloaded' ),
                'content'       => $this->get_layouts_tabs()
            )
        );

        /**
         * Layout selector
         */
         $oFactory->addSettingFields(
            array('layout_general'),
            array(
                'field_id'	=> 'images_link',
                'type'          => 'checkbox',
                'title'		=> __('Clickable Images', 'recipepress-reloaded'),
                'tip'           => __( 'Best used in combination with a lightbox plugin.', 'recipepress-reloaded' ),
                'default'       => true
            ),
            array(
                'field_id'	=> 'print_button_link',
                'type'          => 'checkbox',
                'title'		=> __('Display print button', 'recipepress-reloaded'),
                'tip'           => __( 'Adds a print link to your recipes. It\'s recommended to use one of the numerous print plugins for wordpress to include a print link to ALL of your posts.', 'recipepress-reloaded' ),
                'default'       => true
            ),
            /*array(
                'field_id'	=> 'images_instruction',
                'type'          => 'checkbox',
                'title'		=> __('Instruction Images', 'recipepress-reloaded'),
                'tip'           => __( 'Allow to attach images to instruction steps.', 'recipepress-reloaded' ),
                'default'       => true
            ),*/
            array(
                'field_id'      => 'images_instr_pos',
                'type'          => 'select',
                'title'         => __( 'Position of instruction images', 'recipepress-reloaded' ),
                'tip'           => __( 'Decide wether your instruction images should be display next to the instructions or below.', 'recipepress-reloaded' ),
                'label'         => array(
                    'right'  => __('Right of instruction', 'recipepress-reloaded' ),
                    'below'  => __('Below the instruction', 'recipepress-reloaded' ),
                ),
                'default'       => 'right'
            ),
            array(
                'field_id'	=> 'layout',
                'type'          => 'select',
                'title'		=> __( 'Select a layout', 'recipepress-reloaded' ),
                'tip'           => __( 'Select a layout from the list. More information is provided in the tabs below. There you can also customize the layout chosen.', 'recipepress-reloaded'),
                'description'	=> __( 'Select a layout from the list. More information is provided in the tabs below. There you can also customize the layout chosen.', 'recipepress-reloaded'),
                'label'         => $this->get_layout_selection(),
                'default'       => 'rpr_default'
            )
        );

        /**
         * Create a hidden option for each layout to make the tabs appear
         */
        foreach ( $this->layouts as $id => $meta ) {
            $oFactory->addSettingFields(
                array('layout', $id),
                array(
                    'field_id'  => $id . '_dummy',
                    'type'      => 'hidden'
                )
            );
        }
        /**
         * Include layout options for each layout
         */
        foreach ( $this->layouts as $id => $meta ) {
            if( file_exists( $meta['path'] . '/settings.php' ) ){
                include $meta['path'] . '/settings.php';
            }
        }

    }
    
    /**
     * Create a list of available layouts loacally and globally.
     */
    private function get_layouts_list() {
        /**
         * First create a list of all globally available layouts:
         */
	$dirname = WP_PLUGIN_DIR . '/recipepress-reloaded/public/layouts/';
                
        $this->add_layout_to_list($dirname);
	/**
         * Then also add layouts available locally from the current theme (if applicable)
         */
	$dirname = get_stylesheet_directory() . '/rpr_layouts/';
	
        $this->add_layout_to_list($dirname);
    }
    
    /**
     * Create a list of available layouts at the dir specified
     * The list will be saved as id=>path to the global layouts array
     * @param type $dirname Path where we should search for layouts
     */
    private function add_layout_to_list( $dirname ){
        if ( is_dir( $dirname ) ){
            if ($handle = opendir( $dirname )) {
                // Walk through all folders in that directory:
                while (false !== ($file = readdir($handle))) {
                    if( $file !='.' && $file !='..' && $file != '.svn' ) {
                        if( preg_match( "/plugin/", $dirname ) ){
                            $baseurl = plugins_url() . '/' . 'recipepress-reloaded' . '/public/layouts/' . $file ;
                            $local = false;
                        } else {
                            $baseurl =  get_template_directory_uri(). '/rpr_layouts/'. $file;
                            $local = true;
                        }
                        $this->layouts[$file] = array(
                            'path' => $dirname . $file,
                            'url' => $baseurl,
                            'local' => $local
                        );
                        $this->get_layout_meta( $dirname, $file );
                    }
                }
            }
        }
    }
    
    /**
     * Get an options array for the layouts select field
     * @return array
     */
    private function get_layout_selection() {
        $select = array();
        foreach( $this->layouts as $id => $meta ){
            
            if( $meta['local'] ){
                $select[$id] = '[' . __( 'Local', 'recipepress-reloaded' ) . ']&nbsp;';
            } else {
                $select[$id] = '[' . __( 'RPR', 'recipepress-reloaded' ) . ']&nbsp;';
            }
            $select[$id] .= $meta['title'];
            $select[$id] .= ' - ' . substr( $meta['description'], 0, 50 );
        }
        
        return $select;
    }
    /**
     * Form an array from layouts list to create a tab for each layout.
     * @return type
     */
    private function get_layouts_tabs() {
        $tabs = array();
        
        foreach( $this->layouts as $id => $meta ){
            //$options = $this->layout2option($dirname, $layout);
            if( isset( $this->layouts[$id]['logo'] ) ){
                $title = '<img src="'. $meta['logo'] . '" width="20px" height=20px" style="float:left;" />&nbsp;' . $meta['title'];
            } else {
                $title = $meta['title'];
            }
            $tabs[] = array(
                'section_id'            => $id,
                'section_tab_slug'      => 'layout',
                'title'                 => $title,
                'description'           => $this->get_layout_description( $id )//$meta['description']                
            );
        }

        return $tabs;
    }
    
    private function get_layout_meta( $dirname, $file ) {
        // Param parsing inspired by http://stackoverflow.com/questions/11504541/get-comments-in-a-php-file
	$params=array();
	$filename = $dirname . $file . '/recipe.php';
					
	$docComments = array_filter(
		token_get_all( file_get_contents( $filename ) ), 
			"f_comment"
	);
	
	$fileDocComment = array_shift( $docComments );
					
	$regexp = "/.*\:.*\n/";
	preg_match_all($regexp, $fileDocComment[1], $matches);
	
        foreach( $matches[0] as $match ){
		$param = explode(": ", $match);
		$params[ trim( $param[0] ) ] = trim( $param[1] );
	}
        
        $this->layouts[$file]['description'] = $params['Description'];
        $this->layouts[$file]['title'] = $params['Layout Name'];
        $this->layouts[$file]['author'] = $params['Author'];
        $this->layouts[$file]['author_mail'] = $params['Author Mail'];
        $this->layouts[$file]['author_url'] = $params['Author URL'];
        $this->layouts[$file]['version'] = $params['Version'];
        if( file_exists( $dirname . $file . '/logo.png' ) ){
            $this->layouts[$file]['logo'] = $this->layouts[$file]['url'] . '/logo.png';
        }
        if( file_exists( $dirname . $file . '/screenshot.png' ) ){
            $this->layouts[$file]['screenshot'] = $this->layouts[$file]['url'] . '/screenshot.png';
        }
        // Necessary ??
//        if( file_exists( $dirname . $file . '/print.css' ) ){
//            $this->layouts[$file]['print_css'] = true;
//        } else {
//            $this->layouts[$file]['print_css'] = false;
//        }
    }
    
    /**
     * Returns a nicely formatted description text wth screenshot
     * @param type $layout_id
     * @return string
     */
    private function get_layout_description( $layout_id ) {
        $description = '<span class="layout_description">';
        
        /**
         * Set a local note 
         */
        if( $this->layouts[$layout_id]['local'] ){
            $description .= '<b>' . __( 'This is a local layout provided by your theme.', 'recipepress-reloaded' ) . '</b><br/>';
        }
        /**
         * Include description if available
         */
        if( isset( $this->layouts[$layout_id]['description'] ) && ! empty( $this->layouts[$layout_id]['description'] ) ){
            $description .= $this->layouts[$layout_id]['description'] . '<br/>';
        }
        
        /** 
         * Include version information as far as available
         */
        if( isset( $this->layouts[$layout_id]['version'] ) ){
            $description .= '<br/>';
            $description .= '<b class="layout_desc_label" >' . __( 'Version', 'recipepress-reloaded' ) . ':</b>&nbsp;';
            $description .= '<span>' . $this->layouts[$layout_id]['version'] . '</span>';
        }
        /**
         * Include author and contact information as far as available
         */
        if( isset( $this->layouts[$layout_id]['author'] ) ){
            $description .= '<br/>';
            $description .= '<b class="layout_desc_label" >' . __( 'Author', 'recipepress-reloaded' ) . ':</b>&nbsp;';
            if( isset( $this->layouts[$layout_id]['author_mail'] )){
                $description .= '<a href="mailto:' . $this->layouts[$layout_id]['author_mail'] . '">';
            }
            $description .= $this->layouts[$layout_id]['author'];
            if( isset( $this->layouts[$layout_id]['author_mail'] )){
                $description .= '</a>';
            }
        }
        /**
         * Include homepage if available
         */
        if( isset( $this->layouts[$layout_id]['author_url'] ) ){
            $description .= '<br/>';
            $description .= '<b class="layout_desc_label" >' . __( 'Homepage', 'recipepress-reloaded' ) . ':</b>&nbsp;';
            $description .= '<a href="' . $this->layouts[$layout_id]['author_url'] . '" target="_blank">' . $this->layouts[$layout_id]['author_url'] . '</a>';
        }
        $description .= '</span>';
        /**
         * Include screenshot if available
         */
        if( isset( $this->layouts[$layout_id]['screenshot'] ) ){
            $description .= '<img src="' . $this->layouts[$layout_id]['screenshot'] . '" class="layout_screenshot" />';
        }
        
        return $description;
    }
    
    /*
    private function layout2option($dirname, $file)
{
	// Param parsing inspired by http://stackoverflow.com/questions/11504541/get-comments-in-a-php-file
	$params=array();
	$filename = $dirname . $file . '/recipe.php';
					
	$docComments = array_filter(
		token_get_all( file_get_contents( $filename ) ), 
			"f_comment"
	);
	
	$fileDocComment = array_shift( $docComments );
					
	$regexp = "/.*\:.*\n/";
	preg_match_all($regexp, $fileDocComment[1], $matches);
					
	foreach( $matches[0] as $match ){
		$param = explode(": ", $match);
		$params[ trim( $param[0] ) ] = trim( $param[1] );
	}
					
	$options['selection'] = array(
			'value' => $file,
			'title' => $params['Layout Name'],
			'img' => WP_PLUGIN_URL . '/recipepress-reloaded/layouts/' . $file . '/screenshot.png',
		);
	$options['description'] = array(
		'id' => $file . '_desc',
		'type' => 'info',
    	'title' => $params['Layout Name'],
    	'subtitle' => sprintf( __('Version %s | ', 'recipepress-reloaded' ), $params['Version']),
		'required' => array('rpr_template','equals',$file)
	);
	
	if( isset( $params['Author URL'] ) && $params['Author URL'] != "" )
	{
		$link = $params['Author URL'];
		$link = parse_url($link, PHP_URL_SCHEME) === null ? 'http://' . $link : $link;
		$options['description']['subtitle'].= sprintf( __('By <a href="%s">%s</a> |  ', 'recipepress-reloaded' ), $link, $params['Author']);
	} else {
		$options['description']['subtitle'].= __('By %s |  ', 'recipepress-reloaded' );
	}
	
	if( isset( $params['Author Mail'] ) && $params['Author Mail'] != "" )
	{
		$options['description']['subtitle'].= sprintf( __('<a href="mailto:%s">Contact</a>', 'recipepress-reloaded' ), $params['Author Mail'] );	
	}
	
	if( isset( $params['Description'] ) && $params['Description'] != "" )
	{
		$options['description']['subtitle'].= '</br>' . $params['Description'];	
	}
	
	
	if( strpos($dirname, get_stylesheet_directory() ) !== false){
		$options['selection']['value'] = 'local_'.$file;
		$options['selection']['title'] = strtoupper( __('Local', 'recipepress-reloaded' )) . ' ' . $params['Layout Name'];
		$options['selection']['img'] = get_stylesheet_directory_uri() . '/rpr_layouts/' . $file . '/screenshot.png';
		
		$options['description']['required'] = array('rpr_template','equals','local_'.$file);
	}
	
	return $options;
    }
*/
}

function f_comment( $entry ){
	return $entry[0] == T_COMMENT;
    }