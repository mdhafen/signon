<?php
include_once( '../lib/data.phpm' );

$hash_file = $argv[1];
$dbh = db_connect('core');

if ( is_readable( $hash_file ) ) {
    $handle = fopen( $hash_file, "r" );

    $dbh->query('TRUNCATE pwned_passwords');
    $dbh->beginTransaction();
    $sth = $dbh->prepare('INSERT INTO pwned_passwords (hash,times_seen) VALUES (:hash,:seen)');

    while ( ($word = fgets($handle)) !== false ) {
        list($word,$times) = explode(":",rtrim($word));
        $word = strtolower($word);

        $sth->bindValue( ':hash', $word );
        $sth->bindValue( ':seen', $times );
        $sth->execute();
    }
    fclose( $handle );
    $dbh->commit();
}

?>
