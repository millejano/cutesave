<?php

//
//
// Zuerst an der Api anmelden. Als Rückgabe der login-Methode gibt es eine Session-ID

$client = new SoapClient('http://localhost.magento/magento161/api/soap?wsdl');
try {
    $session = $client->login('soap-test', 'soap-test'); 
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
    exit;
}

//
//
// Jetzt holen wir uns ein Vorlage-Array für Produkte
$basicattributes = $client->call( $session, 'fodcamp_cutesave_product.getbasicattributes');

print_r( $basicattributes );
/*
Array
(
    [sku] =>
    [name] =>
    [price] =>
    [status] => 0
    [description] =>
    [short_description] =>
)
 */

//
//
// Mit den Basic-Attributes wissen wir welche Felder Magento haben möchte,
// diese befüllen wir jetzt logisch
$new_product = $basicattributes;

$new_product['sku'] = '1234';
$new_product['name'] = 'Testname';
$new_product['price'] = '9.95';
$new_product['status'] = '1'; // 1 = Enabled, 0 = Disabled
$new_product['description'] = 'Ich bin ein langer Text';
$new_product['short_description'] = 'Kurzbeschreibung';
$new_product['categories'] = array( 'Kategorie1' );

//
//
// Nun fügen wir das neue Produkt zur Schreib-Queue von Magento hinzu
$client->call( $session, 'fodcamp_cutesave_product.addsimple', array( $new_product ) );


//
//
// Am Ende müssen wir Magento noch mitteilen das alle unsere Änderungen
// geschrieben werden sollen
// Rückgabe kann Fehlermeldungen erhalten (z.B. validierung)

print_r( $client->call($session, 'fodcamp_cutesave_product.write' ) );

//
//
// Wenn wir ganz viele Produkte hinzufügen ist es aber keine gute Idee viele
// Soap-Request zu machen. Dazu gibt es in Magento Multi-Calls

$calls = array();

for($i=0; $i<5; $i++) {

    $multi_new_product = $new_product; // Wenn nehmen das new_product von oben als Vorlage
    $multi_new_product['sku'] = 'test-sku'.$i; // Und ändern nur die Artikel-Nr :)

    $calls[] = array('fodcamp_cutesave_product.addsimple', array( $multi_new_product) );

}
$client->multiCall( $session, $calls );

//
//
// Auch bei einem Multiclass müssen wir danach schreiben
print_r( $client->call($session, 'fodcamp_cutesave_product.write' ) );


//
//
// Im nächsten Schritt legen wir jetzt ein Configuierbares Produkt an.
//
// Dazu muss angegeben werden welche Attribute konfiguiert werden können.

// dann müssen die simple products mit dem gleichen array wie oben übermittelt werden

// und letztlich texte etc. für den konfiguierbaren artikel


$configurable_attributes = array('size', 'color');

$sizes = array('L','M');
$colors = array('Rot','Grün');

$simple_products = array();

//
// Aus Matrix Size * Colors erzeugen wir alle Simple-Products
foreach( $sizes AS $size) {

    foreach( $colors AS $color ) {
        $new_product = array();
        $new_product['sku'] = '1234-'.$color.'-'.$size;
        $new_product['name'] = 'Configurable Test';
        $new_product['price'] = '9.95';
        $new_product['status'] = '1'; // 1 = Enabled, 0 = Disabled
        $new_product['description'] = 'Ich bin ein langer Text';
        $new_product['short_description'] = 'Kurzbeschreibung';

        $new_product['size'] = $size;
        $new_product['color'] = $color;

        $simple_products[] = $new_product;
    }
}

$configurable_product = $basicattributes;
$configurable_product['sku'] = '1234-configurable';
$configurable_product['name'] = 'Test';
$configurable_product['price'] = '9.95';
$configurable_product['status'] = '1'; // 1 = Enabled, 0 = Disabled
$configurable_product['description'] = 'Ich bin ein langer Text';
$configurable_product['short_description'] = 'Kurzbeschreibung';
$configurable_product['categories'] = array( 'Kategorie2' );

$client->call( $session, 'fodcamp_cutesave_product.addconfigurable', array( $configurable_attributes, $simple_products, $configurable_product ) );

print_r( $client->call($session, 'fodcamp_cutesave_product.write' ) );


//
//
// Ganz am Ende müssen wir noch die Indizes aktualisieren
$client->call($session, 'fodcamp_cutesave_product.reindex' );

//
//
// Zur Sicherheit beenden wir die Session
$client->endSession($session);
