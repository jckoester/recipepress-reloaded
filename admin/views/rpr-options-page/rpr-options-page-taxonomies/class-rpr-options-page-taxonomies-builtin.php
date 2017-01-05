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
class RPR_Options_Page_Taxonomies_Builtin {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'rpr_options';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'taxonomies';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'tax_builtin';
	
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug   
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                //'title'             => __( 'Builtin Taxonomies' , 'recipepress-reloaded' ),
                'content'           => array(
                    array(
                        'section_id'    => 'ingredients',
                        'section_tab_slug'	 => 'tax_builtin',
                        'title'         => __( 'Ingredients', 'recipepress-reloaded' ),
                        'description'   => __( 'Ingredients are vital for your recipes. Use these options to set slug and name in case the translation for your language didn\'t do this correctly', 'recipepress-reloaded' ),
                        ),
                    array(
                        'section_id'        => 'category',
                        'section_tab_slug'  => 'tax_builtin',
                        'title'         => __( 'Categories', 'recipepress-reloaded' ),
                        'description'   => __( 'Categories are a built-in taxonomy of wordpress core. You can use them to organize your recipes as well.', 'recipepress-reloaded' ),
                    ),
                    array(
                        'section_id'    => 'post_tag',
			'section_tab_slug'	 => 'tax_builtin',
                        'title'         => __( 'Tags', 'recipepress-reloaded' ),
                        'description'   => __( 'Tags are a built-in taxonomy of wordpress core. You can use them to organize your recipes as well.', 'recipepress-reloaded' ),     
                    )
                )
            )
        );   
      
        /**
        * Add settings fields for ingredients
        */
        $oFactory->addSettingFields(
            array( 'tax_builtin', 'ingredients' ),
				array(
					'field_id'		=> 'id',
					'type'			=> 'hidden',
					'default'		=> 'rpr_ingredient'
				),
                array(
                    'field_id'		=> 'slug',
                    'type'		=> 'text',
                    'title'		=> __( 'Slug', 'recipepress-reloaded' ),
                    'tip'               => __( 'Put in the name of the taxonomy in a machine readable way, using only lower case letters, numbers and hyphens. The slug is the name of the taxonomy as used in the url .', 'recipepress-reloaded'),
                    'description'	=> sprintf( __( 'Ingredient archives will be accessible at <u>%s<b>slug</b>/an-ingredient</u>', 'recipepress-reloaded' ), site_url( '/' ) ),
                    'default'		=> strtolower( __( 'ingredient', 'recipepress-reloaded' ) ),
                ),
				array(
                    'field_id'		=> 'singular',
                    'type'		=> 'text',
                    'title'		=> __( 'Singular name', 'recipepress-reloaded' ),
                    'tip'               => __( 'Put in the name of the taxonomy.', 'recipepress-reloaded' ),
                    'default'		=> __( 'Ingredient', 'recipepress-reloaded' ),
                ),
                array(
                    'field_id'		=> 'plural',
                    'type'		=> 'text',
                    'title'		=> __( 'Plural name', 'recipepress-reloaded' ),
                    'tip'               => __( 'Put in the name of the ingredient in plural.', 'recipepress-reloaded' ),
                    'default'		=> __( 'Ingredients', 'recipepress-reloaded' ),
                ),
                array(
                    'field_id'          => 'error404',
                    'type'              => 'none',
                    'title'             => '<i class="fa fa-exclamation-circle"></i>' . __( ' Error 404?', 'recipepress-reloaded' ),
                    'content'           => __( 'You\'ve set up everything correctly here but now wordpress is giving you an <b>Error 404</b> (not found) ? Try flushing you\'re permalink settings.</br>Visit <i>Settings</i> -> <i>Permalinks</i> and just save without changing anything.', 'recipepress-reloaded' )
                ),
                array(
                    'field_id'          => 'icon_class',
                    'type'              => 'text',
                    'title'             => __( 'Icon class', 'recipepress-reloaded' ),
                    //'description'       => __( 'Define a css selector class here, to display an icon in front of or instead of the taxonomy title.', 'recipepress-reloaded' ),
                    'tip'               => __( 'Define a css selector class here, to display an icon in front of or instead of the taxonomy title.', 'recipepress-reloaded' ),
                    'default'           => 'fa fa-shopping-cart',
                ),
                array(
                    'field_id'          => 'link_target',
                    'type'              => 'select',
                    'title'             => __( 'Link target', 'recipepress-reloaded' ),
                    'tip'               => __( 'Target of links in the ingredient list', 'recipepress-reloaded' ),
                    'default'           => 3,
                    'label'            => array(
                        0 => __('No ingredient links', 'recipepress-reloaded'),
                        1 => __('Only link to ingredient archive page', 'recipepress-reloaded'),
                        2 => __('Custom link if provided, otherwise archive page', 'recipepress-reloaded'),
                        3 => __('Custom links if provided, otherwise no link', 'recipepress-reloaded'),
                    )
                ),
                array(
                    'field_id'          => 'comment_sep',
                    'type'              => 'select',
                    'title'             => __( 'Ingredient note separator', 'recipepress-reloaded' ),
                    'tip'               => __('Decide how to display remarks or comments on your ingredients.', 'recipepress-reloaded'),
                    'default'           => 0,
                    'label'            => array(
                        0 => __('None: 1 egg preferrably free-range or organic', 'recipepress-reloaded'),
                        1 => __('Brackets: 1 egg (preferrably free-range or organic)', 'recipepress-reloaded'),
                        2 => __('Comma: 1 egg, referrably free-range or organic', 'recipepress-reloaded'),
                    )
                ),
                array(
                    'field_id'          => 'auto_plural',
                    'type'              => 'checkbox',
                    'default'           => 0,
                    'title'             => __( 'Automatic pluralization', 'recipepress-reloaded' ),
                    'tip'               => __( 'Automatically create ingredient plurals if more than one is used. If active entering "2 onion" will be rendered as "2 onion<b>s</></br>This only can handle regular plurals. For irregular plurals please enter the correct plural on the ingredients page.', 'recipepress-reloaded' ),
                )
            );      
        
        /**
         * Add options fields for categories
         */
        $oFactory->addSettingFields(
            array( 'tax_builtin', 'category'), 
            array(
                'field_id'          => 'use',
                'type'              => 'checkbox',
                'title'             => __( 'Use categories', 'recipepress-reloaded' ),
                'default'           => false,
            ),
			array(
                'field_id'          => 'icon_class',
                'type'              => 'text',
                'title'             => __( 'Icon class', 'recipepress-reloaded' ),
                //'description'       => __( 'Define a css selector class here, to display an icon in front of or instead of the taxonomy title.', 'recipepress-reloaded' ),
                'tip'               => __( 'Define a css selector class here, to display an icon in front of or instead of the taxonomy title.', 'recipepress-reloaded' ),
                'default'           => 'fa fa-list-ul',
            )
        );

        /**
        * Add options fields for tags
        */
        $oFactory->addSettingFields(
            array( 'tax_builtin', 'post_tag' ), 
            array(
                'field_id'          => 'use',
                'type'              => 'checkbox',
                'title'             => __( 'Use tags', 'recipepress-reloaded' ),
                'default'		=> false,
            ),
			array(
                'field_id'          => 'icon_class',
                'type'              => 'text',
                'title'             => __( 'Icon class', 'recipepress-reloaded' ),
                //'description'       => __( 'Define a css selector class here, to display an icon in front of or instead of the taxonomy title.', 'recipepress-reloaded' ),
                'tip'               => __( 'Define a css selector class here, to display an icon in front of or instead of the taxonomy title.', 'recipepress-reloaded' ),
                'default'           => 'fa fa-tags',
            )
        );
        
        /**
         *  Add validation filters
         */
        add_filter( 
            'validation_' . $oFactory->oProp->sClassName . '_tax_builtin', 
            array( $this, 'replyToValidateSection' ), 
            10, // priority
            4   // number of parameters
        );
    }

     /**
     * Callback functions for validation
     */
    
    
    /**
     * Validates the 'tax_custom' section items.
     * 
     * @callback        filter      validation_{instantiated class name}_{section id}
     */
    public function replyToValidateSection( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) { 

        // Local variables
        $_bIsValid = true;
        $_aErrors  = array();

        // If singular is empty stop precessing and throw an exception
        if ( '' === (string) trim( $aInput['ingredients']['singular'] ) ){
            $_bIsValid = false;
            $_aErrors[ 'section_verification' ] = __( 'Please set at least a singular name for the taxonomy.', 'recipepress-reloaded' );
        }
        // Format slug, if empty replace by singular
        $aInput['ingredients']['slug'] = sanitize_title( $aInput['ingredients']['slug'], sanitize_title( $aInput['ingredients']['singular'] ) );
        // If plural is empty replace by singular + 's'
        if( '' === (string) trim( $aInput['ingredients']['plural'] ) ){
                $aInput['ingredients']['plural'] = trim( $aInput['ingredients']['singular'] ) . __( 's', 'recipepress-reloaded' );
        }
        
        // If error, post a message
        if ( ! $_bIsValid ) {
        
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was an error setting an option in a form section.', 'admin-page-framework-loader' ) );                     
            return $aOldInput;
            
        }     

        // Otherwise, process the data.
        return $aInput;
        
    }      
}