<?php

/**
 * Description
 *
 * An extension of the Sailthru_Client class to use WordPress' HTTP API instead of cURL.
 * Provides a drop in replacement for the PHP5 Sailthru library with improved WordPress integration
 * by replacing all cURL calls with WordPress HTTP API calls.
 */


class WP_Sailthru_Client extends Sailthru_Client {



    /**
     * Prepare JSON payload
     */
    protected function prepareJsonPayload(array $data, array $binary_data = array()) {
        
        // Get the plugin and version and add to API calls
        if ( function_exists( 'get_plugin_data' ) ) {
             $plugin_info = get_plugin_data( __DIR__.'/../plugin.php');
            $version = !empty($plugin_info['Version']) ? $plugin_info['Version'] : '';
        } else {
            $version = '';
        }
        
        $integration = 'WordPress Integration - '. $version;
        $data['integration'] = $integration;

        $payload =  array(
            'api_key' => $this->api_key,
            'format' => 'json', //<3 XML
            'json' => json_encode($data)
        );
        $payload['sig'] = Sailthru_Util::getSignatureHash($payload, $this->secret);
        if (!empty($binary_data)) {
            $payload = array_merge($payload, $binary_data);
        }
        return $payload;
    }


    /**
     * Overload method to transparently intercept calls.
     * Perform an HTTP request using the Wordpress HTTP API
     *
     * @param string $url
     * @param array $data
     * @param string $method
     * @return string
     */
    function httpRequestCurl($url, array $data, $method = 'POST') {

        if ( 'GET' == $method ) {
            $url_with_params = $url;
            if ( count( $data ) > 0 ) {
                $url_with_params .= '?' . http_build_query( $data );
            }
            $url = $url_with_params;
        } else {
            // build a WP approved array
            $data = array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => $data,        // data passed to us by the user
                'cookies' => array()
            );
        }


        if ( 'GET' == $method ) {
            $reply = wp_remote_get( $url, $data );
        } else {
            $reply = wp_remote_post( $url, $data );
        }

        if ( isset( $reply ) ) {
            if ( is_wp_error( $reply ) ) {
                throw new Sailthru_Client_Exception("Bad response received from $url: " . $reply->get_error_message() );
            } else {

                if( wp_remote_retrieve_response_code( $reply ) == 200 ) {
                   return $reply['body'];
                }

                if ( isset ($reply['body']) ) {
                    return $reply['body'];
                } else {
                    return;
                }

            }
        } else {
            throw new Sailthru_Client_Exception( 'A reply was never generated.' );
        }

    }	// end httpRequestCurl()


} // end of WP_Sailthru_Client
