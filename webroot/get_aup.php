<?php
include_once( '../lib/output.phpm' );

$output = array();

$policy_page = 'https://procedure.washk12.org/policy/3000/3700';
$curl = curl_init();
curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $curl, CURLOPT_URL, $policy_page );
$html = curl_exec($curl);
curl_close($curl);

if (!empty($html)) {
	// mb_detect_order("ASCII,UTF-8,ISO-8859-1,windows-1252,iso-8859-15");
	// $encoding = mb_detect_encoding($html);
	$encoding = 'UTF-8';
	$headpos = mb_stripos( $html,'<head>' );
	if ( FALSE !== $headpos ) {
        // assume the policy doesn't have the old charset that DomDocument needs
		$headpos += 6;
		$content = mb_substr( $html, 0, $headpos ) .
			'<meta http-equiv="Content-Type" content="text/html; charset='. $encoding .'">' .
			mb_substr( $html, $headpos );
	}
	$html = mb_convert_encoding( $content, 'HTML-ENTITIES', $encoding );

	$dom = new DomDocument;
	$dom->validateOnParse = false;
	// libxml_use_internal_errors(true)
	$res = @$dom->loadHTML($html);
	if (!$res) { exit; }

	$nodes = $dom->getElementsByTagName('article');
	$article = $nodes->item(0)->cloneNode(true);

	process_node_list( $article->childNodes, $article );
	$output['article'] = $dom->saveHTML( $article );

    output( $output, 'get_aup.tmpl' );
}

function process_node_list( $nodes, $parent ) {
	$length = $nodes->length;
	for ( $i = $length; --$i >= 0; ) {
		$item = $nodes->item($i);
		if ( $item->nodeName == 'nav' ) $parent->removeChild($item);
		else if ( $item->nodeName == 'img' ) $parent->removeChild($item);
		else if ( $item->nodeName == 'a' ) $parent->removeChild($item);
		else if ( $item->hasChildNodes() ) {
			 process_node_list( $item->childNodes, $item );
		}
	}

}
?>
