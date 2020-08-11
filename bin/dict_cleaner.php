<?php
  $dict_file = "/usr/share/dict/words";
  $out_file = "clean_words";
  $dedup = array();

  if ( !empty($argv[1]) && is_readable($argv[1]) ) {
    $dict_file = $argv[1];
  }

  global $ch,$swears,$bad_swears;

  $bad_swears = array(
    'shit','crap','fuck','damn','sex',
  );
  $swears = array(
    'ass','jackass',
  );
/*
  $ch = curl_init();
  curl_setopt( $ch, CURLOPT_COOKIEJAR, "/tmp/dc_cookies.txt" );
  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
  curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)" );
 */

  if ( is_readable( $dict_file ) ) {
    $handle = fopen( $dict_file, "r" );

    $out = fopen( $out_file, "w+" );
    if ( $out === false ) {
      error_log("Error openning file for output: $out_file\n");
      exit;
    }

    while ( ($word = fgets($handle)) !== false ) {
      $word = rtrim( $word );
      $word = mb_strtolower($word);
//echo "$word len: ". mb_strlen($word) ." quote: ". strpos($word,"'") ." offensive: ". print_r(is_offensive($word),true) ."\n";
      if (
        mb_strlen($word) < 4 || mb_strlen($word) > 8 ||
        strpos($word,"'") !== false || is_offensive($word) === "true" ||
        ! mb_detect_encoding($word,"ASCII",true)
      ) {
        continue;
      }
      else {
        if ( empty($dedup[$word]) ) {
          $dedup[$word] = 1;
        }
      }
    }
    foreach ( $dedup as $word => $count ) {
      if ( substr($word,-1) == 's' ) {
        if ( substr($word,-3) == 'ies' ) {
          if ( !empty($dedup[substr($word,0,-3).'y']) ) {
            continue;
          }
        }
        else {
          if ( !empty($dedup[substr($word,0,-1)]) ) {
            continue;
          }
        }
      }
      fwrite( $out, $word ."\n" );
    }
    fclose( $handle );
    fclose( $out );
  }

function is_offensive( $word ) {
  global $ch,$swears,$bad_swears;

  foreach ( $bad_swears as $s ) {
    if ( stripos( $word, $s ) !== false ) {
      return true;
    }
  }
  foreach ( $swears as $s ) {
    if ( $word == $s || stripos($word,$s) === 0 ) {
      return true;
    }
  }
  return false;

  /*
  curl_setopt( $ch, CURLOPT_URL, 'http://www.wdyl.com/profanity?q='. $word );
  $json = curl_exec( $ch );
  $result = json_decode( $json, true );

  return $result['response'];
   */
}
?>
