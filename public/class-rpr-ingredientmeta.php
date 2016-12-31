<?php
/**
 * Additional fields for the ingredient post type
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/public
 */

// Include the library file. Set your file path here.
include( dirname( dirname( __FILE__ ) ) . '/libraries/apf/admin-page-framework.php' );

/**
 * Additional fields for the ingredient post type
 *
 * Adds some additional fields for the ingredients taxonomy like plural, description (rich text) or image
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/public
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_IngredientMeta extends AdminPageFramework_TermMeta {
    
    /**
     * Use the setUp() method to define settings of this taxonomy fields.
     */
    public function setUp() {

        /*
         * Adds setting fields into the meta box.
         */
        $this->addSettingFields(
            array(
                'field_id'      => 'plural',
                'type'          => 'text',
                'title'         => __( 'Plural name', 'recipepress-reloaded' ),
                'description'   => sprintf( __( 'The plural name of the ingredient. If empty the default pluralization "<b>%s</b>" will be used.', 'recipepress-reloaded' ), __( 's', 'recipepress-reloaded' ) ),
                'attributes'    => array(
                    'size'  => 40
                ),
                //'help'          => 'This is help text.',
                //'help_aside'    => 'This is additional help text which goes to the side bar of the help pane.',
            ),
            array(
                'field_id'      => 'image',
                'type'          => 'image',
                'title'         => __( 'Image Upload', 'admin-page-framework-loader' ),
                'attributes'    => array(
                    'preview' => array(
                        'style' => 'max-width: 200px;',
                    ),
                ),                
            ),
            array(
                'field_id'      => 'use_in_list',
                'type'          => 'checkbox',
                'title'         => __( 'Use in listings', 'recipepress-reloaded' ),
                'tip'           => __( 'Disable, if you don\'t want this ingredient to appear in ingredient listing. You probably don\'t want to have a list of all recipes using salt, sugar, etc. ', 'recipepress-reloaded' ),
                'default'       => true
            )
        );     
    
        // Customize the sorting algorithm of the terms of a custom column.
        add_filter( 'get_terms', array( $this, 'replyToSortCustomColumn' ), 10, 3 );
    
    }
        
    /*
     * ( optional ) modify the columns of the term listing table
     */
    public function sortable_columns_RPR_IngredientMeta( $aColumn ) { // sortable_column_{instantiated class name}
        
        return array( 
                'plural' => 'plural',
            ) 
            + $aColumn;
        
    }

    
    public function columns_RPR_IngredientMeta( $aColumn ) { // column_{instantiated class name}
        
        unset( $aColumn['description'] );
        $posts = $aColumn['posts'];
        unset( $aColumn['posts'] );
        //var_dump($aColumn);
        return array( 
                'cb' => $aColumn['cb'],
                'thumbnail' => __( 'Thumbnail', 'recipepress-reloaded' ),
            ) 
            + $aColumn
            + array(
                'plural' => __( 'Plural name', 'recipepress-reloaded' ),
                'posts' => $posts,
            );
        
    }
    
    /*
     * ( optional ) output the stored option to the custom column
     */    
    public function cell_RPR_IngredientMeta( $sCellHTML, $sColumnSlug, $iTermID ) { // cell_{instantiated class name}
        
        if ( ! $iTermID || $sColumnSlug != 'thumbnail' ) { return $sCellHTML; }
        
        $aOptions = get_option( 'RPR_IngredientMeta', array() ); // by default the class name is the option key.
        return isset( $aOptions[ $iTermID ][ 'image_upload' ] ) && $aOptions[ $iTermID ][ 'image_upload' ]
            ? "<img src='{$aOptions[ $iTermID ][ 'image_upload' ]}' style='max-height: 72px; max-width: 120px;'/>"
            : $sCellHTML;
        
    }
    
    public function cell_RPR_IngredientMeta_plural( $sCellHTML, $iTermID ) { // cell_{instantiated class name}_{cell slug}
        return get_term_meta( $iTermID, 'plural', true );
        // Using AdminPageFramework::getOption() is another way to retrieve an option value.
        //return AdminPageFramework::getOption( 'RPR_IngredientMeta', array( $iTermID, 'plural' ) );            
        
    }
    
    public function cell_RPR_IngredientMeta_use_in_list( $sCellHTML, $iTermID ) { // cell_{instantiated class name}_{cell slug}
        
        // Using AdminPageFramework::getOption() is another way to retrieve an option value.
        return AdminPageFramework::getOption( 'RPR_IngredientMeta', array( $iTermID, 'use_in_list' ) );            
        
    }
    
    /**
     * Customizes the sorting algorithm of a custom column.
     */
    public function replyToSortCustomColumn( $aTerms, $aTaxonomies, $aArgs ) {
        
        if ( 'edit-tags.php' == $GLOBALS['pagenow'] && isset( $_GET{'orderby'} ) && 'custom' == $_GET{'orderby'} ) {
            usort( $aTerms, array( $this, '_replyToSortByCustomOptionValue' ) );
        }
        return $aTerms;
        
    }
        public function _replyToSortByCustomOptionValue( $oTermA, $oTermB ) {
            
            $_sClassName = get_class( $this ); // the instantiated class name is the option key by default.
            $_sTextFieldA = AdminPageFramework::getOption( $_sClassName, array( $oTermA->term_id, 'plural' ) );
            $_sTextFieldB = AdminPageFramework::getOption( $_sClassName, array( $oTermB->term_id, 'plural' ) );
            return isset( $_GET['order'] ) && 'asc' == $_GET['order']
                ? strnatcmp( $_sTextFieldA, $_sTextFieldB )
                : strnatcmp( $_sTextFieldB, $_sTextFieldA );
            
        }    
    
    
    /*
     * ( optional ) Use this method to validate submitted option values.
     */
    public function validation_RPR_IngredientMeta( $aNewOptions, $aOldOptions ) {

        // Do something to compare the values.
        return $aNewOptions;
    }
    
}   