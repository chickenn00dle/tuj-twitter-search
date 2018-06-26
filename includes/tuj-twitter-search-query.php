<?php
// Main query function. creates query array with q, count, result_type, tweet_mode and include_entities params and adds oauth header for validation
function queryTwitter($search) 
{
    $options = get_option('tujts_options');
    $url = "https://api.twitter.com/1.1/search/tweets.json";
    $search = $search != "" ? "#" . $search : "";

    // TODO: Add count and twitter user to options page and remove static values from query object.
    $query = array( 
        'count' => 5, 
        'q' => 'from:tujweb ' . urlencode($search), 
        'result_type' => 'recent', 
        'tweet_mode' => 'extended',
        'include_entities' => true,
    );

    $oauth_access_token = $options['tujts_field_oath_token'];
    $oauth_access_token_secret = $options['tujts_field_oath_secret'];
    $consumer_key = $options['tujts_field_consumer_key'];
    $consumer_secret = $options['tujts_field_consumer_secret'];

    $oauth = array(
        'oauth_consumer_key' => $consumer_key,
        'oauth_nonce' => time(),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_token' => $oauth_access_token,
        'oauth_timestamp' => time(),
        'oauth_version' => '1.0'
    );

    $base_params = empty($query) ? $oauth : array_merge($query, $oauth);
    $base_info = buildBaseString($url, 'GET', $base_params);
    $url = empty($query) ? $url : $url . "?" . http_build_query($query);

    $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;

    $header = array(buildAuthorizationHeader($oauth), 'Expect:');
    $options = array( CURLOPT_HTTPHEADER => $header,
                      CURLOPT_HEADER => false,
                      CURLOPT_URL => $url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_SSL_VERIFYPEER => false);

    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);
    curl_close($feed);
    return  json_decode($json);
}

// construct url from base url and params array. used by queryTwitter
function buildBaseString($baseURI, $method, $params)
{
    $r = array(); 
    ksort($params);
    foreach($params as $key=>$value){
        $r[] = "$key=" . rawurlencode($value); 
    }
    return $method. "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r)); 
}

// construct oauth heading from oath array. used by queryTwitter
function buildAuthorizationHeader($oauth)
{
    $r = 'Authorization: OAuth '; 
    $values = array(); 
    foreach($oauth as $key=>$value)
        $values[] = "$key=\"" . rawurlencode($value) . "\""; 
    $r .= implode(', ', $values); 
    return $r; 
}