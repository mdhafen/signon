<?php
  $dict_file = "/usr/share/dict/words";
  $out_file = "clean_words";
  $dedup = array();

  if ( !empty($argv[1]) && is_readable($argv[1]) ) {
    $dict_file = $argv[1];
  }

  global $ch,$swears,$bad_swears,$watchout;

  $bad_swears = array(
    'shit','crap','fuck','damn','sex',
  );
  $swears = array(
    'ass','jackass','naked','nude','adult','lesbian','breast','drug','jesus',
    'christ','lingerie','virgin','strip','livecam','thong','fetish','amateur',
    'mature','spank','webcam','facial','torture','bikini','suck','deviant',
    'exotic','explicit','pee','mount','tit','abortion','butt','screw','escort',
    'breed','mistress','nudist','lick','celeb','prostate','sperm','intimate',
    'niger','voyuer','erotic','wiley','mating',
  );
  $watchout = array(
    'disney','angeles','kill','siemens','nintendo','toyota','cialis','tramadol',
    'flickr','slave','mozilla','phpbb','xanax','verizon','bizrate','levitra',
    'freebsd','valium','adidas','suzuki','chrysler','adipex','kelkoo','mazda',
    'fujitsu','medicaid','minolta','lexmark','hotmail','findlaw','logitech',
    'belkin','porsche','zshops','worldcat','hitachi','batman','coleman',
    'warcraft','lexus','expedia','ferrari','hyundai','nextel','nvidia',
    'propecia','thinkpad','chevy','sparc','cingular','simpsons','subaru',
    'fioricet','paxil','prozac','sanyo','garmin','barbie','zoloft','ultram',
    'allah',
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
/*
 * Don't worry about plurals and such.
 *
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
 */
      fwrite( $out, $word ."\n" );
    }
    fclose( $handle );
    fclose( $out );
  }

function is_offensive( $word ) {
  global $ch,$swears,$bad_swears,$watchout;

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
  foreach ( $watchout as $s ) {
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
