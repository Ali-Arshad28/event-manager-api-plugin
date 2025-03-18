<?php

/**
 * Plugin Name: Event Manager Plugin
 * Description: Handle Event CRUD Operations
 * Version: 1.0
 * Author: Ali Arshad
 */

// Define the main plugin class
class Event_Manager_API {

    // Constructor to initialize hooks
    public function __construct() {
        add_action('init', array($this, 'register_event_routes')); // Register API Routes
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts')); // Enqueue Scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts')); // Enqueue frontend Scripts
        register_activation_hook(__FILE__, array($this, 'create_event_table')); // Create table on activation
        add_action('admin_menu', array($this, 'em_register_admin_menu')); // Register Admin Menu
        add_shortcode("view-events", array($this, "view_events"));
    }

    // Enqueue necessary scripts for the form
    public function enqueue_scripts() {
      // Debugging the path      
      wp_enqueue_script('event-manager-api', plugin_dir_url(__FILE__).'js/event-manager.js', array(), '1.0.0', true);
  
      // Localize the script to pass the API URL and nonce
      wp_localize_script('event-manager-api', 'eventManager', array(
          'apiUrl' => rest_url('event-manager/v1/'),
          'nonce'  => wp_create_nonce('wp_rest'),
      ));

      // for enqueueing CSS for Admin page
     wp_enqueue_style('admin-event-page-style', plugin_dir_url(__FILE__).'assets/style.css', array(), '1.0.0', true);


  }

        public function enqueue_frontend_scripts() {
            wp_enqueue_script( 'view-events-js', plugin_dir_url(__FILE__).'js/view-events.js', array(), '1.0.0', true);

            // Localize the script to pass the API URL and nonce
            wp_localize_script('view-events-js', 'eventManager', array(
            'apiUrl' => rest_url('event-manager/v1/'),
            'nonce'  => wp_create_nonce('wp_rest'),
    ));
        }
  

    // Register REST API Routes for CRUD operations
    public function register_event_routes() {
        register_rest_route('event-manager/v1/', 'events', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_event'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ));

        register_rest_route('event-manager/v1/', 'events/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_event'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ));

        register_rest_route('event-manager/v1/', 'events', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_events'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ));

        register_rest_route('event-manager/v1', 'events/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_event'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ));

        register_rest_route('event-manager/v1', 'events/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_event'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ));
    }


    //   custom table for events on plugin activation
    public function create_event_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'events'; // The name of the table
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            date DATETIME NOT NULL,
            location VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Handle Admin Menu
    public function em_register_admin_menu() {
        add_menu_page(
            'Event Manager',       // Page title
            'Event Manager',       // Menu title
            'manage_options',      // Capability
            'event-manager',       // Menu slug
            array($this, 'em_event_manager_page'), // Callback function
            'dashicons-calendar-alt', // Icon
            6                      // Position
        );
    }

  /*  public function create_event(){

      if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_nonce']) && wp_verify_nonce($_POST['event_nonce'], 'event_form_nonce')) {
        // Sanitize and validate input
        $name = sanitize_text_field($_POST['name']);
        $date = sanitize_text_field($_POST['date']);
        $location = sanitize_text_field($_POST['location']);

        global $wpdb;

        // Insert into database
        $wpdb->insert(
            $wpdb->prefix . 'events',
            array(
                'name'     => $name,
                'date'     => $date,
                'location' => $location,
            )
        );

        // Display message
        if ($wpdb->insert_id) {
            echo '<div class="notice notice-success"><p>Event added successfully!</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Failed to add event.</p></div>';
        }
    }

    }   */



    public function create_event(WP_REST_Request $request) {
      // Get parameters from the request
      $name = sanitize_text_field($request->get_param('name'));
      $date = sanitize_text_field($request->get_param('date'));
      $location = sanitize_text_field($request->get_param('location'));
  
      global $wpdb;
  
      // Insert into the database
      $wpdb->insert(
          $wpdb->prefix . 'events',
          array(
              'name'     => $name,
              'date'     => $date,
              'location' => $location,
          )
      );
  
      // Return response
      if ($wpdb->insert_id) {
          return new WP_REST_Response(array('status' => 'success', 'message' => 'Event added successfully!'), 200);
      } else {
          return new WP_REST_Response(array('status' => 'error', 'message' => 'Failed to add event.'), 500);
      }
  }

  function delete_event(WP_REST_Request $request) {

    global $wpdb;
    $deleted = $wpdb->delete(
        $wpdb->prefix . 'events',
        array(
            'id' => $request->get_param('id')
        )

        );

    if ($deleted) {
        return new WP_REST_Response(
            array(
                'status' => 'success',
                'message' => 'Event deleted successfully.',
            ),
            200
        );
    } else {
        return new WP_REST_Response(
            array(
                'status' => 'error',
                'message' => 'Failed to delete event or event does not exist.',
            ),
            400
        );
    }

        
  }
  
    // Admin Page Callback
    public function em_event_manager_page() {
    
      ob_start();

      include_once plugin_dir_path(__FILE__). "templates/event-form.php";

      $template = ob_get_contents();

      ob_end_clean();

      echo $template;
 
    }


    // function to display the events in front-end
    public function view_events() {

        ob_start();

        include_once plugin_dir_path(__FILE__). "templates/view-events.php";
    
        return ob_get_clean();

    }

    public function get_events(WP_REST_Request $request) {
        // Get the global $wpdb object for database interaction
        global $wpdb;
    
        // Define the table name with the WordPress prefix
        $table_name = $wpdb->prefix . 'events';
    
        // Query the database to get all events
        $events = $wpdb->get_results("SELECT * FROM $table_name");
    
        // Check if any events were found
        if (empty($events)) {
            // If no events, return a 404 response
            return new WP_REST_Response(array('status' => 'error', 'message' => 'No events found'), 404);
        }
    
        // Prepare the events data to be returned
        $events_data = array();
    
        foreach ($events as $event) {
            $events_data[] = array(
                'id'        => (int) $event->id,
                'name'      => sanitize_text_field($event->name),
                'date'      => $event->date,  // You can format the date if needed
                'location'  => sanitize_text_field($event->location),
            );
        }
    
        // Return the list of events in the response
        return new WP_REST_Response($events_data, 200);
    }
    
    public function update_event(WP_REST_Request $request) {

        $name = sanitize_text_field($request->get_param('name'));
        $date = sanitize_text_field($request->get_param('date'));
        $location = sanitize_text_field($request->get_param('location'));
        $id = sanitize_text_field($request->get_param('id'));
  
        global $wpdb;
    
        // Insert into the database
        $updated = $wpdb->update(
            $wpdb->prefix . 'events',
            array(
                'name'     => $name,
                'date'     => $date,
                'location' => $location,
            ),
            array(
                'id' => $id
            )
        );
    
        // Return response
        if ($updated) {
            return new WP_REST_Response(
                array(
                    'status' => 'success',
                    'message' => 'Event updated successfully.',
                ),
                200
            );
        } else {
            return new WP_REST_Response(
                array(
                    'status' => 'error',
                    'message' => 'Failed to update event or event does not exist.',
                ),
                400
            );
        }

    }

}

// Initialize the plugin
new Event_Manager_API();
