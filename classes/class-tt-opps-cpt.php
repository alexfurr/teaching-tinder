<?php

$tt_opps = new tt_opps();

class tt_opps
{



	//~~~~~
	function __construct ()
	{
		$this->addWPActions();
	}


/*	---------------------------
	PRIMARY HOOKS INTO WP
	--------------------------- */
	function addWPActions ()
	{
		//Admin Menu
		add_action( 'init',  array( $this, 'create_CPTs' ) );

		add_action( 'admin_menu', array( $this, 'create_admin_pages' ));

		add_action( 'add_meta_boxes_tt_opp', array( $this, 'add_metaboxes' ));


		add_filter( 'manage_tt_opp_posts_columns', array( $this, 'my_custom_post_columns' ), 10, 2 );
		add_action('manage_tt_opp_posts_custom_column', array($this, 'my_custom_post_content'), 10, 2);

        // Register Taxonomies
        add_action( 'init',  array($this, 'register_taxonomies'), 0 );


		// Add 'Instructions' title to the text editor for projects
		//add_action( 'edit_form_after_title', array($this, 'myprefix_edit_form_after_title') );


		// Add Default order of DATE to the project list edit table
		//add_filter('pre_get_posts', array($this, 'peer_projects_default_order'));

		// Save additional project meta for the custom post
		add_action( 'save_post_tt_opp', array($this, 'save_meta' ), 10, 3);



	}


/*	---------------------------
	ADMIN-SIDE MENU / SCRIPTS
	--------------------------- */
	function create_CPTs ()
	{


        $singular = 'Teaching Opportunity';
        $plural = 'Teaching Opportunities';

        //Topics
        $labels = array(
           'name'               =>  $plural,
           'singular_name'      =>  $singular,
           'menu_name'          =>  $plural,
           'name_admin_bar'     =>  $plural,
           'add_new'            =>  'Add New '.$singular,
           'add_new_item'       =>  'Add New '.$singular,
           'new_item'           =>  'New '.$singular,
           'edit_item'          =>  'Edit '.$singular,
           'view_item'          => 'View '.$plural,
           'all_items'          => 'All '.$plural,
           'search_items'       => 'Search '.$plural,
           'parent_item_colon'  => '',
           'not_found'          => 'No '.$plural.' found.',
           'not_found_in_trash' => 'No '.$plural.' found in Trash.'
        );

        $args = array(
           'menu_icon' => 'dashicons-groups',
           'labels'             => $labels,
           'public'             => true,
           'publicly_queryable' => true,
           'show_ui'            => true,
           'show_in_nav_menus'	 => true,
           'show_in_menu'       => true,
           'query_var'          => true,
           'rewrite' => array( 'slug' => 'opportunities' ),
           'capability_type'    => 'page',
           'has_archive'        => false,
           'hierarchical'       => false,
           'supports'           => array( 'title', 'editor', 'revisions' )

        );

        register_post_type( 'tt_opp', $args );
	}

	function create_admin_pages()
	{

        $parent_slug = "no_parent";
        $page_title="Opportunity Dates";
        $menu_title="";
        $menu_slug="tt-opp-dates";
        $function=  array( $this, 'draw_opp_dates_page' );
        $myCapability = "edit_others_pages";
        add_submenu_page($parent_slug, $page_title, $menu_title, $myCapability, $menu_slug, $function);

        $parent_slug = "edit.php?post_type=tt_opp";
        $page_title="Settings";
        $menu_title="Settings";
        $menu_slug="tt-settings";
        $function=  array( $this, 'draw_settings_page' );
        $myCapability = "edit_others_pages";
        add_submenu_page($parent_slug, $page_title, $menu_title, $myCapability, $menu_slug, $function);


	}


	function draw_opp_dates_page()
	{
		include_once( ICL_TEACHING_TINDER_PATH . '/admin/opp_dates.php' );
	}

    function draw_settings_page()
    {
        include_once( ICL_TEACHING_TINDER_PATH . '/admin/settings.php' );
    }


    public static function get_meta_items()
    {
        $meta_items_array = array(
            "event_type",
            "start_date",
            "end_date",
            "location",
            "start_time",
            "role_type",
            "renumeration",
            "cohort",
            "module",
            "who",
            "primary_contact",
            "primary_contact_email",
            "interest_email_contact",

        );

        return $meta_items_array;

    }





	// Register the metaboxes on projects CPT
	function add_metaboxes ()
	{

		//Main settings
		$id 			= 'tt_meta';
		$title 			= 'Main Information';
		$drawCallback 	= array( $this, 'draw_metabox_main_info' );
		$screen 		= 'tt_opp';
		$context 		= 'normal';
		$priority 		= 'default';
		$callbackArgs 	= array();

		add_meta_box(
			$id,
			$title,
			$drawCallback,
			$screen,
			$context,
			$priority,
			$callbackArgs
		);


        //Students
        $id 			= 'tt_meta_students';
        $title 			= 'Students';
        $drawCallback 	= array( $this, 'draw_metabox_students_info' );
        $screen 		= 'tt_opp';
        $context 		= 'side';
        $priority 		= 'default';
        $callbackArgs 	= array();

        add_meta_box(
            $id,
            $title,
            $drawCallback,
            $screen,
            $context,
            $priority,
            $callbackArgs
        );

	}


	function draw_metabox_main_info($post, $metabox)
	{

		//add wp nonce field
		wp_nonce_field( 'save_metabox_tt_opp', 'metabox_tt_opp' );

        $item_id = $post->ID;

		// GEt the post meta
        $item_meta = get_post_meta($item_id);

        $item_keys = tt_opps::get_meta_items();

        foreach ($item_keys as $this_key)
        {
            $$this_key = ''; // Defualt set to blank
            if(array_key_exists($this_key, $item_meta) )
            {
                $$this_key = $item_meta[$this_key][0];
            }
        }

        $args = array(
            "type" => "textbox",
            "name" => "renumeration",
            "value" => $renumeration,
            "ID" => "renumeration",
            "label" => "Recognition",
            "width" => 400,
        );
        echo ek_forms::form_item($args);



        $args = array(
            "type" => "textbox",
            "name" => "primary_contact",
            "value" => $primary_contact,
            "ID" => "primary_contact",
            "label" => "Primary contact name for more questions",
            "width" => 400,

        );
        echo ek_forms::form_item($args);

        $args = array(
            "type" => "textbox",
            "name" => "primary_contact_email",
            "value" => $primary_contact_email,
            "ID" => "primary_contact_email",
            "label" => "Primary contact email for more questions",
            "width" => 400,

        );
        echo ek_forms::form_item($args);

        $args = array(
            "type" => "textbox",
            "name" => "interest_email_contact",
            "value" => $interest_email_contact,
            "ID" => "interest_email_contact",
            "label" => "Who receives an email from expressions of interest (email address)",
            "width" => 400,

        );
        echo ek_forms::form_item($args);





        $event_types = teaching_tinder_queries::get_cat_items('tt_event_type');
        $args = array(
            "type" => "dropdown",
            "name" => "event_type",
            "value" => $event_type,
            "ID" => "event_type",
            "label" => "Event Type",
            "options" => $event_types,
        );
        echo ek_forms::form_item($args);

        $role_types = teaching_tinder_queries::get_cat_items('tt_role_type');
        $args = array(
            "type" => "dropdown",
            "name" => "role_type",
            "value" => $role_type,
            "ID" => "role_type",
            "label" => "Role Type",
            "options" => $role_types,
        );
        echo ek_forms::form_item($args);


        // Add dates
        $args = array(
            "type" => "textarea",
            "RTE"   => true,
            "name" => "Who do we need?",
            "value" => $who,
            "ID" => "who",
            "label" => "Who do we need?",
        );

        echo ek_forms::form_item($args);



	}


    function draw_metabox_students_info($post, $metabox)
    {

        $item_id = $post->ID;

        // GEt the post meta
        $item_meta = get_post_meta($item_id);
        $item_keys = tt_opps::get_meta_items();

        foreach ($item_keys as $this_key)
        {
            $$this_key = ''; // Defualt set to blank
            if(array_key_exists($this_key, $item_meta) )
            {
                $$this_key = $item_meta[$this_key][0];
            }
        }

        $my_cohorts = teaching_tinder_queries::get_cat_items('tt_cohort');
        $args = array(
            "type" => "dropdown",
            "name" => "cohort",
            "value" => $cohort,
            "ID" => "cohort",
            "label" => "Cohort",
            "options" => $my_cohorts,
        );
        echo ek_forms::form_item($args);


        $my_modules = teaching_tinder_queries::get_cat_items('tt_module');
        $args = array(
            "type" => "dropdown",
            "name" => "module",
            "value" => $module,
            "ID" => "module",
            "label" => "Module",
            "options" => $my_modules,
        );
        echo ek_forms::form_item($args);

    }




	// Save metabox data on edit slide
	function save_meta ( $postID, $post, $update )
	{



        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['metabox_tt_opp'], 'save_metabox_tt_opp' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( ! current_user_can( 'edit_post', $postID ) ) {
            return;
        }



        $item_keys = tt_opps::get_meta_items();

        foreach ($item_keys as $this_key)
        {
            $this_value	= isset( $_POST[$this_key] ) ? $_POST[$this_key] : '';
            update_post_meta( $postID, $this_key, $this_value );
        }
	}



	function my_custom_post_columns( $columns )
	{

        unset($columns['date']);
        $columns['event_type'] = 'Event Type';
        $columns['role_type'] = 'Role Type';
        $columns['cohort'] = 'Cohort';
        $columns['opp_dates'] = 'Available Dates ';


        return $columns;
	}



	// Content of the custom columns for Topics Page
	function my_custom_post_content($column_name, $post_ID)
	{
		switch ($column_name)
		{

            case "event_type":
                $event_type = get_post_meta($post_ID, 'event_type', true);
                $event_type_name = get_term( $event_type )->name;
                echo $event_type_name;

            break;

            case "role_type":
                $role_type = get_post_meta($post_ID, 'role_type', true);
                $role_type_name = get_term( $role_type )->name;
                echo $role_type_name;

            break;

            case "cohort":
                $cohort = get_post_meta($post_ID, 'cohort', true);
                echo get_term( $cohort )->name;
            break;

            case "opp_dates":
                echo '<a href="options.php?page=tt-opp-dates&opp_id='.$post_ID.'" class="button-primary">Opportunity Dates</a>';


            break;

		}
	}



    /**
    *  Registers the taxonomies.
    *  ---
    */
   public static function register_taxonomies ()
   {

       $args = array(
           'label'                 => 'Cohorts',
           'singular_label'        => 'Cohort',
           'public'                => true,
           'hierarchical'          => false,
           'show_ui'               => true,
           'show_in_quick_edit'    => false,
           'meta_box_cb'           => false,
           'show_in_nav_menus'     => true,
           'show_admin_column'     => false,
           'show_in_rest'          => true,
           'show_tagcloud'         => false,
           'query_var'         => true,
       );
       register_taxonomy( 'tt_cohort', array( 'tt_opp'), $args );

       $args = array(
           'label'                 => 'Event Types',
           'singular_label'        => 'Event Type',
           'public'                => true,
           'hierarchical'          => false,
           'show_ui'               => true,
           'show_in_quick_edit'    => false,
           'meta_box_cb'           => false,
           'show_in_nav_menus'     => true,
           'show_admin_column'     => false,
           'show_in_rest'          => true,
           'show_tagcloud'         => false,
           'query_var'         => true,
       );
       register_taxonomy( 'tt_event_type', array( 'tt_opp'), $args );

       $args = array(
           'label'                 => 'Role Types',
           'singular_label'        => 'Role Type',
           'public'                => true,
           'hierarchical'          => false,
           'show_ui'               => true,
           'show_in_quick_edit'    => false,
           'meta_box_cb'           => false,
           'show_in_nav_menus'     => true,
           'show_admin_column'     => false,
           'show_in_rest'          => true,
           'show_tagcloud'         => false,
           'query_var'         => true,
       );
       register_taxonomy( 'tt_role_type', array( 'tt_opp'), $args );

       $args = array(
           'label'                 => 'Modules',
           'singular_label'        => 'Module',
           'public'                => true,
           'hierarchical'          => false,
           'show_ui'               => true,
           'show_in_quick_edit'    => false,
           'meta_box_cb'           => false,
           'show_in_nav_menus'     => true,
           'show_admin_column'     => false,
           'show_in_rest'          => true,
           'show_tagcloud'         => false,
           'query_var'         => true,
       );
       register_taxonomy( 'tt_module', array( 'tt_opp'), $args );

       $args = array(
           'label'                 => 'Cohorts',
           'singular_label'        => 'Cohort',
           'public'                => true,
           'hierarchical'          => false,
           'show_ui'               => true,
           'show_in_quick_edit'    => false,
           'meta_box_cb'           => false,
           'show_in_nav_menus'     => true,
           'show_admin_column'     => false,
           'show_in_rest'          => true,
           'show_tagcloud'         => false,
           'query_var'         => true,
       );
       register_taxonomy( 'tt_cohort', array( 'tt_opp'), $args );

       $args = array(
           'label'                 => 'Locations',
           'singular_label'        => 'Location',
           'public'                => true,
           'hierarchical'          => false,
           'show_ui'               => true,
           'show_in_quick_edit'    => false,
           'meta_box_cb'           => false,
           'show_in_nav_menus'     => true,
           'show_admin_column'     => false,
           'show_in_rest'          => true,
           'show_tagcloud'         => false,
           'query_var'         => true,
       );
       register_taxonomy( 'tt_location', array( 'tt_opp'), $args );




   }

} //Close class
?>
