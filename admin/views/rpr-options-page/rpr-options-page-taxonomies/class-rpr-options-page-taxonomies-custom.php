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
class RPR_Options_Page_Taxonomies_Custom {
    
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
    public $sSectionID  = 'tax_custom';
	        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'        => $this->sSectionID,
                'tab_slug'          => $this->sTabSlug,
                'section_tab_slug'  => 'tax_custom',
                'title'             => __( 'Custom Taxonomies', 'recipepress-reloaded' ),
                'description'       => sprintf( __( 'Use this to create your custom taxonomy structure. Use the <b>+</b> sign on the right to add more taxonomies.<br/>Have a look at the <a href="%s" target="_blank">documentation</a> for some inspiration.', 'recipepress-reloaded' ), 'https://github.com/dasmaeh/recipepress-reloaded/wiki'), 
		'repeatable'        => true,
                'sortable'          => true,
            )
        );   
      
        /**
        * Add Fields to custom taxonomies
        */
        $oFactory->addSettingFields(
            //array( 'tax_custom', 'custom'),
            'tax_custom',
            array(
                'field_id'	 => 'tab_title',
                'type'		 => 'section_title',
                /*'label'		 => __( 'Name', 'recipepress-reloaded' ),*/
                'attributes' => array(
                    'size'      => 10,
                    'type'      => 'text', // change the input type
                ),
               /* 'default'   => __( 'New taxonomy', 'recipepress-reloaded' ),*/
            ),
            array(
                'field_id'	=> 'slug',
                'type'          => 'text',
                'title'		=> __( 'Slug', 'recipepress-reloaded' ),
                'tip'           => __( 'Put in the name of the taxonomy in a machine readable way, using only lower case letters, numbers and hyphens. The slug is the name of the taxonomy as used in the url .', 'recipepress-reloaded'),
                'description'	=> sprintf( __( 'Ingredient archives will be accessible at <u>%s<b>slug</b>/an-ingredient</u>', 'recipepress-reloaded' ), site_url( '/' ) ),
            ),
            array(
                'field_id'	=> 'singular',
                'type'		=> 'text',
                'title'		=> __( 'Singular name', 'recipepress-reloaded' ),
                'tip'           => __( 'Put in the name of the taxonomy.', 'recipepress-reloaded' ),
                //'default'		=> __( 'Ingredient', 'recipepress-reloaded' ),
            ),
            array(
                'field_id'	=> 'plural',
                'type'		=> 'text',
                'title'		=> __( 'Plural name', 'recipepress-reloaded' ),
                'tip'           => __( 'Put in the name of the ingredient in plural.', 'recipepress-reloaded' ),
                //'default'		=> __( 'Ingredients', 'recipepress-reloaded' ),
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
                'default'           => 'fa fa-tags',
            ),
			array(
				'field_id'			=> 'property_id',
				'type'				=> 'text',
				'title'				=> __( 'Structured data property', 'recipepress-reloaded' ),
				'tip'				=> __( 'Property id for the recipe schema according to schema.org', 'recipepress-reloaded' ),
				'description'		=> __( 'Structured data property id for the recipe schema according to <a href="http://1.schemaorgae.appspot.com/Recipe" target="_blank">schema.org</a>.', 'recipepress-reloaded' ),
				'default'			=> ''
			),
            array(
                'field_id'          => 'hierarchical',
                'type'              => 'checkbox',
                'title'             => __( 'Hierarchical', 'recipepress-reloaded' ),
                'tip'               => __( 'Check to allow terms of this taxonomy to be nested (like categories).', 'recipepress-reloaded' ),
                'default'           => false
            ),
            array(
                'field_id'          => 'filter',
                'type'              => 'checkbox',
                'title'             => __( 'Show filter', 'recipepress-reloaded' ),
                'tip'               => __( 'Check to allow filtering recipes for this taxonomy in admin view.', 'recipepress-reloaded' ),
                'default'           => false
            ),
            array(
                'field_id'          => 'table',
                'type'              => 'checkbox',
                'title'             => __( 'Show in table', 'recipepress-reloaded' ),
                'tip'               => __( 'Check to show this taxonomy in the recipes table in admin view.', 'recipepress-reloaded' ),
                'default'           => false
            )
        );
                
        /**
         *  Add validation filters
         */
        add_filter( 
            'validation_' . $oFactory->oProp->sClassName . '_tax_custom', 
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
        
        foreach ( $aInput as $input_tax ) {
            // If tab title and singular is empty stop precessing and throw an exception
            if ( '' === (string) trim( $input_tax['tab_title'] ) && '' === (string) trim( $input_tax['singular'] ) ){
                $_bIsValid = false;
                $_aErrors[ 'section_verification' ] = __( 'Please set at least a title and a name for the taxonomy.', 'recipepress-reloaded' );
            }
            // If tab_title is empty, replace by singular
            if( '' === (string) trim( $input_tax['tab_title'] ) ){
                $input_tax['tab_title'] = trim( $input_tax['singular'] );
            }
            // If singular is empty, replace by tab_title
            if( '' === (string) trim( $input_tax['singular'] ) ){
                $input_tax['singular'] = trim( $input_tax['tab_title'] );
            }
            // Format slug, if empty replace by singular
            $input_tax['slug'] = sanitize_title( $input_tax['slug'], sanitize_title( $input_tax['singular'] ) );
            // If plural is empty replace by singular + 's'
            if( '' === (string) trim( $input_tax['plural'] ) ){
                $input_tax['plural'] = trim( $input_tax['singular'] ) . __( 's', 'recipepress-reloaded' );
            }
            $aOutput[] = $input_tax;
        }

        // If error, post a message
        if ( ! $_bIsValid ) {
        
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was an error setting an option in a form section.', 'admin-page-framework-loader' ) );                     
            return $aOldInput;
            
        }     
   
        // Otherwise, process the data.
        return $aOutput;
        
    }      
}