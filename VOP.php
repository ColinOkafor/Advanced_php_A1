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


//Function to generate HTML table rows for volunteer entries and styled based off the selected hours
function volunteer_entry($row, $use_color = false){
    $style ='';
    if($use_color){
        if($row->Hours <10){
            $style = 'style="background-color:#c6efce;"';
        }elseif($row->Hours >= 10 $$ $row->Hours <=100){
            $style = 'style="background-color:#fff2cc;"';
        }else{
            $style = 'style="background-color:#f4cccc;"';

        }
    }
        return '<tr '.$style.'>
                            <td>' . esc_html($row->Position) . '</td>
                            <td>' . esc_html($row->Organization) . '</td>
                            <td>' . esc_html($row->Type) . '</td>
                            <td>' . esc_html($row->Email) . '</td>
                            <td>' . esc_html($row->Description) . '</td>
                            <td>' . esc_html($row->Location) . '</td>
                            <td>' . esc_html($row->Hours) . '</td>
                            <td>' . esc_html($row->Skills_Required) . '</td>
                        </tr>'
}

//Function to generate full HTML table
function volunteer_table($rows, $use_color = false){
    if(empty($rows)) return '<p><em>No Volunteer Opportunities found.</em></p>';

    $output = '<table class="volunteer-table" border="1" cellpadding = "10" cellspacing="0" style="margin:0 auto;">
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
                <tbody>';
    foreach($rows as $row){
        $output .= volunteer_entry($row, $use_color);
    }
    $output .= '</tbody></table>';
    return $output;

}



//Wordpress Voluneer opportunity shortcode
//the id starts from 4 due to bug fixes.
function wporg_shortcode($atts=[], $content=null){
    global $wpdb;
    $atts = shortcode_atts([
        'hours'=>'',
        'type'='',
    ], $atts);

    //Build query condition
    $where = []
    if($atts['hours' !== '']){
        $where[] = 'Hours <'.intval($atts['hours']); 
    }
     if($atts['type' !== '']){
        $where[] = 'Type = "'.esc_sql($atts['type']).'"'; 
    }

    $query = "SELECT * FROM Vps";

   
    if(empty($result)){
       return "No Voluneer Opportunity found";
    }
    $result = $wpdb->get_results($query);

    //If no parameters, use coloring
    $use_color = ($atts['hours'] === '' && $atts ['type'] === '');
    return volunteer_table($results, $use_color);
}
add_shortcode('volunteer_positions', 'wporg_shortcode');


//Admin page for managing volunteers
function wp_volunteer_adminpage_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) return;

    global $wpdb;
    $table = 'Vps';

    //delete handler
    if(isset($_GET['delete'])){
        $wpdb->delete($table,['VpID'=> intval($_GET['delete'])]);
        echo '<div class="notice notice-success"><p>Volunteer opportunity deleted.</p></div>';
    }

    //create/update form handler
    if(isset($_POST['volunteer_submit'])){
        $position = sanitize_text_field($_POST['position']);
        $organization = sanitize_text_text_field($_POST['organization']);
        $type = sanitize_text_field($_POST['type']);
        $email = sanitize_text_email($_POST['email']);
        $description = sanitize_text_textarea_field($_POST['description']);
        $location = sanitize_text_text_field($_POST['location']);
        $hours = intval($_POST['hours']);
        $skills = sanitize_textarea_field($_POST['skills']);

        $errors = [];
        if(empty($position)) $errors[] = 'Position is required.';
        if(empty($organization)) $errors[] = 'Organization is required.';
        if(!is_email($email)) $errors[] = 'Valid email is required.';
        if(empty($errors)) {
            $id = intval($_POST['VpID'] ?? 0);
                if($id > 0){
                    //update
                    $wpdb->update($table, [
                        'Position'=>$position,
                        'Organization'=>$organization,
                        'Type'=>$type,
                        'Email'=>$email,
                        'Description'=>$description,
                        'Location'=>$location,
                        'Hours'=>$hours,
                        'Skills_Required'=>$skills
                    ],['VpID'=>$id]);
                    echo '<div class="notice notice-success"><p>Volunteer opportunity updated.</p></div>';
                }else{
                    //insert
                    $wpdb->insert($table,[
                        'Position'=>$position,
                        'Organization'=>$organization,
                        'Type'=>$type,
                        'Email'=>$email,
                        'Description'=>$description,
                        'Location'=>$location,
                        'Hours'=>$hours,
                        'Skills_Required'=>$skills
                    ]);
                    echo '<div class="notice notice-success"><p>Volunteer opportunity added.</p></div>';
                }
        }else{
                echo '<div class="notice notice-success"><p>'.$errors.'</p></div>';

            }

    }

    //Load exisiting data if editing
    $row = null;
    if(isset($_GET['edit'])){
        $row = $wpdb->get_row("SELECT * FROM $table WHERE VpID=".intval($_GET['edit']));
    }
    ?>
    <div class="wrap">
        <h1><?=$row ? 'Edit' : 'Add' ?>Volunteer Opportunity</h1>
        <form method='post'>
            <input type="hidden" name="VpID" value="<?= $row->VpID ?? ''?>">
            <table class="form-table">
                <tr>
                    <th><label for="position">Position</label></th>
                    <td><input type="text" name="position" id="position" value="<?= esc_attr($row->Position ?? '') ?>" required></td>
                </tr>
                 <tr>
                    <th><label for="organization">Organization</label></th>
                    <td><input type="text" name="organization" id="organization" value="<?= esc_attr($row->Organization ?? '') ?>" required></td>
                </tr>
                <tr>
                    <th><label for="type">Type</label></th>
                    <td>
                        <select name="type" id="type" required>
                            <option value>Select Type</option>
                            <?php
                                $types = ['Full-time', 'Part-time', 'Remote', 'Seasonal'];
                                foreach($types as $t){
                                    $selected = (($row-> Type ?? '')==$t)?'selected':'';
                                    echo "<option value ='$t' $selected>$t</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="email">Email</label></th>
                    <td><input type="email" name="email" id="email" value="<?= esc_attr($row->Email ?? '') ?>" required></td>
                </tr>
                <tr>
                    <th><label for="description">Description</label></th>
                    <td><textarea name="description" id="description"><?= esc_textarea($row->Description ?? '') ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="location">Location</label></th>
                    <td><input type="text" name="location" id="location" value="<?= esc_attr($row->Location ?? '') ?>" required></td>
                </tr>
                <tr>
                    <th><label for="hours">Hours</label></th>
                    <td><input type="text" name="hours" id="hours" value="<?= esc_attr($row->Hours ?? '') ?>" required></td>
                </tr>
               <tr>
                    <th><label for="skills">SKills Required</label></th>
                    <td><textarea name="skills" id="skills"><?= esc_textarea($row->Skills_Required ?? '') ?></textarea></td>
                </tr>
            </table>  
            <p class="submit">
                <input type="submit" name="volunteer_submit" id="submit" class="button button-primary" value="<?= $row ? 'Update':'Add'?> Volunteer">
            </p>  

        </form>
    </div>
    <?php
}


function wp_volunteer_adminpage() {
add_menu_page(
'Volunteer',
'volunteer',
'manage_options',
'Volunteer_manager',
'wp_volunteer_adminpage_html',
'dashicons-groups', // could give a custom icon here
6
);
}
add_action( 'admin_menu', 'wp_volunteer_adminpage' );
?>