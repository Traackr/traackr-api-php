<?php
namespace Traackr;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;

abstract class TraackrApiObject
{
    public static $connectionTimeout = 10;
    public static $timeout = 10;
    public static $sslVerifyPeer = true;
    public static $maxConcurrentRequests = 10;
    public static $maxRetries = 3;

    private $curl;
    private $guzzleClient;
    private $logger;

    // Headers passed with each request
    private $curl_headers = [
        // Adding some headers to force no caching.
        'Cache-Control: no-cache',
        'Pragma: no-cache',

        //some proxies throw a "417" error for CURL calls; CURL is supposed
        //to retry the call, but doesn't, so just set "Expect" to nothing to
        //avoid this (this ensures that CURL doesn't set it to an unrecognized
        //value under the covers)
        'Expect:',

        // To Ensure the server sends back UTF-8 text
        'Accept-Charset: utf-8',
        'Accept: */*'
    ];

    public function __construct($concurrent = false)
    {
        $this->logger = TraackrAPI::getLogger();
        if ($concurrent) {
            $stack = HandlerStack::create(new CurlHandler());
            $stack->push(\GuzzleHttp\Middleware::retry($this->createGuzzleRetryHandler()));
            $this->guzzleClient = new Client([
                'connect_timeout' => self::$connectionTimeout,
                'timeout' => self::$timeout,
                'verify' => self::$sslVerifyPeer,
                'handler' => $stack
            ]);
        } else {
            // init cURL
            $this->curl = curl_init();
        }
    }

    private function createGuzzleRetryHandler()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ) {
            if ($retries >= self::$maxRetries) {
                return false;
            }
            if ($response->getStatusCode() != 500) {
                return false;
            }
            $this->logger->debug(sprintf(
                'Retrying %s %s %s/%s, %s',
                $request->getMethod(),
                $request->getUri(),
                $retries + 1,
                self::$maxRetries,
                $response ? 'status code: ' . $response->getStatusCode() : $exception->getMessage()
            ), [$request->getHeader('Host')[0]]);
            return true;
        };
    }

    private function getGuzzleHeaders()
    {
        $headers = [];
        foreach ($this->curl_headers as $header) {
            $parts = explode(':', $header);
            $property = $parts[0];
            $value = '';
            if (!empty($parts[1])) {
                $value = trim($parts[1]);
            }
            $headers[$property] = $value;
        }
        $headers['Accept-Encoding'] = 'gzip;q=1.0, deflate;q=0.5, identity;q=0.1';
        return $headers;
    }

    /**
     * Initialize self::$curl with the base settings all request types use.
     */
    private function initCurlOpts()
    {
        // clear any existing opts
        curl_reset($this->curl);
        // return value as a string
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        // Set timeouts
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, self::$connectionTimeout);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, self::$timeout);
        // Set encodings
        curl_setopt($this->curl, CURLOPT_ENCODING, 'gzip;q=1.0, deflate;q=0.5, identity;q=0.1');
        // SSL verify peer
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, self::$sslVerifyPeer);
    }

    protected function checkRequiredParams($params, $fields)
    {
        foreach ($fields as $f) {
            // empty(false) returns true so need extra test for that
            if (empty($params[$f]) && !(isset($params[$f]) && is_bool($params[$f]))) {
                throw new MissingParameterException('Missing parameter: ' . $f);
            }
        }
    }

    /**
     * influencers/lookup & /search and posts/lookup and /search now support multiple
     * customer-keys, so this function massages arrays of them.
     */
    protected function addCustomerKey(&$params)
    {
        $key = TraackrApi::getCustomerKey();
        if (!empty($key) && empty($params[PARAM_CUSTOMER_KEY])) {
            $params[PARAM_CUSTOMER_KEY] = $key;
        }

        if (!empty($params[PARAM_CUSTOMER_KEY]) && is_array($params[PARAM_CUSTOMER_KEY])) {
            $params[PARAM_CUSTOMER_KEY] = implode(',', $params[PARAM_CUSTOMER_KEY]);
        }

        return $params;
    }

    /*
     * Make best attempt at converting booleans.
     * Boolean type should be passed to the API but this function will also
     * handle their string representation ('true' and 'false')
     */
    protected function convertBool($params, $key)
    {
        // Does key even exists?
        if (!isset($params[$key])) {
            return 'false';
        }

        $bool = $params[$key];

        if (is_bool($bool)) {
            return $bool ? 'true' : 'false';
        }

        if (strtolower($bool) === 'true') {
            return 'true';
        }

        return 'false';
    }

    // Prepare parameters before any GET or POST call.
    // For now any pass-thru parameter passed as a true or false boolean
    // is converted to a string since that's what the API expects
    private function prepareParameters($params)
    {
        foreach ($params as $key => $value) {
            if ($params[$key] === true) {
                $params[$key] = 'true';
            }

            if ($params[$key] === false) {
                $params[$key] = 'false';
            }
        }

        return $params;
    }

    private function handleErrorResponse($url, $httpCode, $body)
    {
        if ($httpCode == '400') {
            // Let's try to see if it's a bad customer key
            if ($body === 'Customer key not found') {
                $message = 'Invalid Customer Key (HTTP 400)';

                $this->logger->error($message);

                throw new InvalidCustomerKeyException(
                    $message . ': ' . $body,
                    $httpCode
                );
            }

            $message = 'Missing or Invalid argument/parameter (HTTP 400)';

            $this->logger->error($message);

            throw new MissingParameterException(
                $message . ': ' . $body,
                $httpCode
            );
        }

        if ($httpCode == '403') {
            $message = 'Invalid API key (HTTP 403)';
            $this->logger->error($message);

            throw new InvalidApiKeyException(
                $message . ': ' . $body,
                $httpCode
            );
        }

        if ($httpCode == '404') {
            $message = 'API resource not found (HTTP 404)';

            $this->logger->error($message);

            throw new NotFoundException(
                $message . ': ' . $url,
                $httpCode
            );
        }

        $message = 'API HTTP Error (HTTP ' . $httpCode . ')';

        $this->logger->error($message);

        throw new TraackrApiException(
            $message . ': ' . $body,
            $httpCode
        );
    }

    private function call($decode, $contentTypeHeader)
    {
        // Prep headers
        curl_setopt(
            $this->curl,
            CURLOPT_HTTPHEADER,
            array_merge($this->curl_headers, [$contentTypeHeader], TraackrApi::getExtraHeaders())
        );

        // Make the call!
        $curl_exec = curl_exec($this->curl);

        $logger = TraackrAPI::getLogger();

        if ($curl_exec === false) {
            $info = curl_getinfo($this->curl);
            $message = 'API call failed (' . $info['url'] . '): ' . curl_error($this->curl);

            $this->logger->error($message);

            throw new TraackrApiException($message);
        }

        if (null === $curl_exec) {
            $message = 'API call failed. Response was null.';

            $this->logger->error($message);

            throw new TraackrApiException($message);
        }

        $httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if ($httpCode != '200') {
            $info = curl_getinfo($this->curl);
            $this->handleErrorResponse($info['url'], $httpCode, $curl_exec);
        }

        // API MUST return UTF8
        if ($decode) {
            $rez = json_decode($curl_exec, true);
        } else {
            $rez = $curl_exec;
        }

        return null === $rez ? false : $rez;
    }

    public function get($url, $params = [])
    {
        $this->initCurlOpts();
        // Add API key parameter if not present
        $api_key = TraackrApi::getApiKey();
        if (!isset($params[PARAM_API_KEY]) && !empty($api_key)) {
            $params[PARAM_API_KEY] = $api_key;
        }

        // Add params if needed
        if (!empty($params)) {
            // Prepare params
            $params = $this->prepareParameters($params);
            $url .= '?' . http_build_query($params);
        }

        // Sets URL
        curl_setopt($this->curl, CURLOPT_URL, $url);
        // Make call
        $this->logger->debug('Calling (GET): ' . $url);

        return $this->call(!TraackrAPI::isJsonOutput(), 'Content-Type: application/json;charset=utf-8');
    }

    public function getConcurrent(array $requests)
    {
        // build requests
        $headers = $this->getGuzzleHeaders();
        $headers['Content-Type'] = 'application/json;charset=utf-8';
        $guzzleRequests = function ($requests) use ($headers) {
            foreach ($requests as $request) {
                $params = $this->prepareParameters($request['params']);
                // Add API key parameter if not present
                $api_key = TraackrApi::getApiKey();
                if (!isset($params[PARAM_API_KEY]) && !empty($api_key)) {
                    $params[PARAM_API_KEY] = $api_key;
                }
                $queryString = http_build_query($params);
                $url = $request['url'] . '?' . $queryString;
                $this->logger->debug('Calling (GET)[concurrent]: ' . $url);
                yield new Request('GET', $url, $headers);
            }
        };

        $results = [];
        // queue up requests
        $pool = new Pool($this->guzzleClient, $guzzleRequests($requests), [
            'concurrency' => self::$maxConcurrentRequests,
            'fulfilled' => function (Response $response, $index) use (&$results, $requests) {
                $httpCode = $response->getStatusCode();
                if ($httpCode !== 200) {
                    $this->handleErrorResponse($requests[$index]['url'], $httpCode, $response->getBody());
                }
                if (!TraackrAPI::isJsonOutput()) {
                    $rez = json_decode($response->getBody(), true);
                } else {
                    $rez = $response->getBody();
                }
                $results[] = null === $rez ? false : $rez;
            },
            'rejected' => function (RequestException $e) {
                // TODO: give consumer an option to
                // continue with some failed requests?
                $url = $e->getRequest()->getUri();
                $message = 'API call failed (' . $url . '): ' . $e->getMessage();
                $this->logger->error($message);
                throw new TraackrApiException($message);
            },
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();

        return $results;
    }

    public function post($url, $params = [], $isJson = false)
    {
        $this->initCurlOpts();
        // POST call
        curl_setopt($this->curl, CURLOPT_POST, 1);

        // Build Parameters
        // Add API key parameter if not present; API key always passed as a query
        // string even for POST
        $api_key = TraackrApi::getApiKey();
        if (!empty($api_key)) {
            $url .= '?' . PARAM_API_KEY . '=' . $api_key;
        }

        // Sets URL
        curl_setopt($this->curl, CURLOPT_URL, $url);

        if (!$isJson) {
            // Prepare params
            $params = $this->prepareParameters($params);
            // Sets params
            $http_param_query = http_build_query($params);
        } else {
            $http_param_query = json_encode($params);
        }
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $http_param_query);

        // Make call
        $this->logger->debug('Calling (POST): ' . $url . ' [' . $http_param_query . ']');

        return $this->call(!TraackrAPI::isJsonOutput(), $isJson ? 'Content-Type: application/json;charset=utf-8' : 'Content-Type: application/x-www-form-urlencoded;charset=utf-8');
    }

    // Support for HTTP DELETE Methods
    public function delete($url, $params = [])
    {
        $this->initCurlOpts();
        // Build Parameters
        // Add API key parameter if not present; API key always passed as a query
        // string even for DELETE
        $api_key = TraackrApi::getApiKey();
        if (!empty($api_key)) {
            $url .= '?' . PARAM_API_KEY . '=' . $api_key;
        }

        // Sets URL
        curl_setopt($this->curl, CURLOPT_URL, $url);

        // Prepare and set params
        $params = $this->prepareParameters($params);
        $http_param_query = http_build_query($params);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $http_param_query);
        // Sets URL
        curl_setopt($this->curl, CURLOPT_URL, $url);
        // Set Custom Request for DELETE
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        // Make call
        $this->logger->debug('Calling (DELETE): ' . $url);

        return $this->call(!TraackrAPI::isJsonOutput(), 'Content-Type: application/json;charset=utf-8');
    }
}
