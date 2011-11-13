<?php

$client = new SoapClient('http://localhost.magento/magento161/api/soap?wsdl');
$session = $client->login('soap-test', 'soap-test');

// daten vorbereiten
$new_product = $client->call( $session, 'fod_cutesave_product.getbasicattributes');

$new_product['sku'] = '1234';
$new_product['name'] = 'Testname';
$new_product['price'] = '9.95';
$new_product['status'] = '1'; // 1 = Enabled, 0 = Disabled
$new_product['description'] = 'Ich bin ein langer Text';
$new_product['short_description'] = 'Kurzbeschreibung';
$new_product['categories'] = array( 'Kategorie1' );

// 1000 simple products importieren
$calls = array();
for($i=0; $i<1000; $i++) {

    $multi_new_product = $new_product; // Wenn nehmen das new_product von oben als Vorlage
    $multi_new_product['sku'] = 'speedtest-sku'.$i; // Und ändern nur die Artikel-Nr :)

    $calls[] = array('fod_cutesave_product.addsimple', array( $multi_new_product) );

}
$calls[] = array('fod_cutesave_product.write', array() );

$client->multiCall( $session, $calls );


// Aufruf per shell
// time php speedtest.php

// Ergebnis (auf meinem Desktop)
/*
real	0m11.632s
user	0m0.228s
sys	0m0.048s
 */