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
    $wpdb -> query("CREATE TABLE wp_vps(
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
    $wpdb->query("INSERT INTO wp_vps (Position) VALUES ('Food Bank Volunteer'),
                                    (Organization) VALUES ('Hamilton caregivers'),
                                    (Type) VALUES ('seasonal'),
                                    (E-mail) VALUES ('johndoe123@yahoo.com'),
                                    (Description) VALUES ('An opportunity to help the homeless by distributing food around jackson square'),
                                    (Location) VALUES ('50, main street west, Hamilton'),
                                    (Hours) VALUES ('5'),
                                    (Skills_Required) VALUES ('Social work');");
}
register_activation_hook(__FILE__, 'myplugin_activate');

?>