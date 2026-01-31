<?php
/**
 * Plugin Name: Volunteer Opportunity Plugin
 * Description: A plugin that allows users to participate in activities that interests them.
 * Author: Colin Okafor
 * Version: 1.0
 */
//Activation code
function myplugin_activate(){
    global $wpdb;
    $wpdb -> query("CREATE TABLE Vps(
    VpID int NOT NULL AUTO_INCREMENT,
    Position varchar(255),
    Organization varchar(255),
    Type varchar(255),
    Email varchar(255),
    Description TEXT,
    Location varchar(255),
    Hours INT,
    Skills_Required varchar(255),
    PRIMARY KEY(VpID)
    );");
    
    $wpdb->query("INSERT INTO Vps (Position, 
                                    Organization, 
                                    Type, 
                                    Email, 
                                    Description,
                                    Location, 
                                    Hours, 
                                    Skills_Required)
                                    VALUES('Food Bank Volunteer', 
                                            'Hamilton caregivers', 
                                            'seasonal',
                                            'johndoe123@yahoo.com', 
                                            'An opportunity to help the homeless by distributing food around jackson square',
                                            '50 Main street West, Hamilton',
                                            5,
                                            'social work')");
}
register_activation_hook(__FILE__, 'myplugin_activate');


//Deactivation Hook
function myplugin_deactivate(){
    global $wpdb;
    $wpdb->query("DELETE FROM Vps;");
}
register_deactivation_hook(__FILE__, 'myplugin_deactivate');

//Plugin css file handler
function volunteer_plugin_styles(){
    wp_enqueue_style(
        'volunteer-table-style',
        plugin_dir_url(__FILE__) . 'css/volunteer-table.css'
    );
}
add_action('wp_enqueue_scripts', 'volunteer_plugin_styles');
//Function to generate HTML for one Volunteer entry in form of a table
function volunteer_entry($row){
    return '<table class="volunteer-table" border="1" cellpadding="10" cellspacing="0" style ="margin:0 auto;">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Organization</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Hours</th>
                        <th>Skills Required</th>
                    </tr>
                </thead>
                    <tbody>
                        <tr>
                            <td>' . esc_html($row->Position) . '</td>
                            <td>' . esc_html($row->Organization) . '</td>
                            <td>' . esc_html($row->Type) . '</td>
                            <td>' . esc_html($row->Email) . '</td>
                            <td>' . esc_html($row->Description) . '</td>
                            <td>' . esc_html($row->Location) . '</td>
                            <td>' . esc_html($row->Hours) . '</td>
                            <td>' . esc_html($row->Skills_Required) . '</td>
                        </tr>
                </tbody>
            </table>';
}
//Wordpress Voluneer opportunity shortcode
//the id starts from 4 due to bug fixes.
function wporg_shortcode($atts=[], $content=null){
    global $wpdb;
    $atts = shortcode_atts(
        array(
            'id' => 0,
        ),
        $atts
    );
    $query = "SELECT * FROM Vps";
    $result = $wpdb->get_results($query);

   
      if(empty($result)){
       return "No Voluneer Opportunity found";
       }
    
       $output = '';
       foreach ($result as $row){
            $output .= volunteer_entry($row);
       }
    return $output;
}
add_shortcode('volunteer_positions', 'wporg_shortcode');
?>