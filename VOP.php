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
?>