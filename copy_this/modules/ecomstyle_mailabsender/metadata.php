<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';
 
/**
 * Module information
 */
$aModule = array(
    'id'           => 'mailabsender',
    'title'        => '<strong style="color:#04B431;">e</strong><strong>ComStyle.de</strong>:  <i>Mailabsender</i>',
    'description'  => array(
        'de' => 'Als Absender der Bestellmails wird der Name des Kunden verwendet.',
    ),
    'thumbnail'    => '',
    'version'      => '1.0',
    'thumbnail'    => 'ecomstyle.png',
    'author'       => '<strong style="font-size: 17px;color:#04B431;">e</strong><strong style="font-size: 16px;">ComStyle.de</strong>',
    'email'          => 'info@ecomstyle.de',
    'url'          => 'http://ecomstyle.de',
    'extend'       => array(
        'oxemail'     => 'ecomstyle_mailabsender/mailabsender',
    ),

);
?>