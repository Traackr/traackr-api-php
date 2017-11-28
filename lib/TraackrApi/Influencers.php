<?php

namespace Traackr;

class Influencers extends TraackrApiObject
{
    /**
     * Get an influencer's data
     *
     * @param string $uid
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function show($uid, $p = array('with_channels' => false))
    {
        if (empty($uid)) {
            throw new MissingParameterException("Missing Influencer UID parameter");
        }

        // API Object
        $inf = new Influencers();

        //Sanitize default values
        $p['with_channels'] = $inf->convertBool($p, 'with_channels');

        // Add customer key + check required params
        $p = $inf->addCustomerKey($p);
        $inf->checkRequiredParams($p, array('with_channels'));
        // support for multi params
        $uid = is_array($uid) ? implode(',', $uid) : $uid;

        return $inf->get(TraackrApi::$apiBaseUrl . 'influencers/show/' . $uid, $p);
    }

    /**
     * Returns an influencer's connections
     *
     * @param string $uid
     * @param string $direction
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function connections($uid, $direction = '')
    {
        if (empty($uid)) {
            throw new MissingParameterException("Missing Influencer UID parameter");
        }

        $uid = is_array($uid) ? implode(',', $uid) : $uid;
        $direction = empty($direction) ? '' : $direction . '/';
        $inf = new Influencers();
        return $inf->get(TraackrApi::$apiBaseUrl . 'influencers/connections/' . $direction . $uid, []);
    }

    /**
     * Lookup Influencer by a Twitter handle
     *
     * @param string $username
     * @param string $type USERNAME | TWITTER_ID
     * @return bool|mixed
     * @throws MissingParameterException
     * @throws \UnexpectedValueException
     */
    public static function lookupTwitter($username, $type = 'USERNAME')
    {
        if (empty($username)) {
            throw new MissingParameterException("Missing username parameter");
        }

        if ('USERNAME' !== $type && 'TWITTER_ID' !== $type) {
            throw new \UnexpectedValueException('Type parameter must be "USERNAME" or "TWITTER_ID".');
        }

        $inf = new Influencers();

        $parameters = [
            'type' => $type
        ];

        return $inf->get(TraackrApi::$apiBaseUrl . 'influencers/lookup/twitter/' . $username, $parameters);
    }

    /**
     * Add Twitter account
     *
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function addTwitter($p = array())
    {
        $inf = new Influencers();

        $p = $inf->addCustomerKey($p);
        $inf->checkRequiredParams($p, array('customer_key'));

        // Validate business requirements
        if (empty($p['username']) && empty($p['twitter_id'])) {
            throw new MissingParameterException("Either username or twitter_id must be present");
        } else if (!empty($p['username']) && !empty($p['twitter_id'])) {
            throw new MissingParameterException("Only one of username or twitter_id may be present");
        }

        // support multi params
        if (!empty($p['tags'])) {
            $p['tags'] = is_array($p['tags']) ? implode(',', $p['tags']) : $p['tags'];
        }

        return $inf->post(TraackrApi::$apiBaseUrl . 'influencers/add/twitter', $p);
    }

    /**
     * Add influencer by name and primary URL
     *
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function add($p = array())
    {
        $inf = new Influencers();

        $p = $inf->addCustomerKey($p);
        $inf->checkRequiredParams($p, array('name', 'url', 'customer_key'));

        // support multi params
        if (!empty($p['tags'])) {
            $p['tags'] = is_array($p['tags']) ? implode(',', $p['tags']) : $p['tags'];
        }

        return $inf->post(TraackrApi::$apiBaseUrl . 'influencers/add', $p);
    }

    /**
     * Add a tag to an influencer
     *
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function tagAdd($p = array('strict' => false))
    {
        $inf = new Influencers();

        // Sanitize default values
        $p['strict'] = $inf->convertBool($p, 'strict');

        $p = $inf->addCustomerKey($p);
        $inf->checkRequiredParams($p, array('influencers', 'tags', 'customer_key', 'strict'));

        // support for multi params
        $p['influencers'] = is_array($p['influencers']) ?
            implode(',', $p['influencers']) : $p['influencers'];
        $p['tags'] = is_array($p['tags']) ?
            implode(',', $p['tags']) : $p['tags'];

        return $inf->post(TraackrApi::$apiBaseUrl . 'influencers/tag/add', $p);
    }

    /**
     * Remove a tag from an influencer
     *
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function tagRemove($p = array('all' => false))
    {
        $inf = new Influencers();

        // Sanitize default values
        $p['all'] = $inf->convertBool($p, 'all');

        $p = $inf->addCustomerKey($p);
        // 'influencers' is not required if 'all' is set to true
        // by then 'all' has already be converted to a string
        if ($p['all'] === 'false') {
            $inf->checkRequiredParams($p, array('influencers', 'tags', 'customer_key', 'all'));
        } else {
            $inf->checkRequiredParams($p, array('tags', 'customer_key', 'all'));
        }

        // support for multi params
        if (!empty($p['influencers'])) {
            $p['influencers'] = is_array($p['influencers']) ?
                implode(',', $p['influencers']) : $p['influencers'];
        }
        $p['tags'] = is_array($p['tags']) ?
            implode(',', $p['tags']) : $p['tags'];

        return $inf->post(TraackrApi::$apiBaseUrl . 'influencers/tag/remove', $p);
    }

    /**
     * Get the tags on an influencer
     *
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function tagList($p = array(
        'is_prefix' => false,
        'type' => 'person'))
    {
        $inf = new Influencers();

        // Sanitize default values
        $p['is_prefix'] = $inf->convertBool($p, 'is_prefix');

        $p = $inf->addCustomerKey($p);
        $inf->checkRequiredParams($p, array('tag', 'is_prefix', 'customer_key'));

        return $inf->get(TraackrApi::$apiBaseUrl . 'influencers/tag/list', $p);
    }

    /**
     * Lookup influencers
     *
     * @param array $p
     * @return bool|mixed
     */
    public static function lookup($p = array(
        'is_tag_prefix' => false,
        'gender' => 'all',
        'type' => 'person',
        'enable_tags_aggregation' => false,
        'enable_country_aggregation' => false,
        'enable_audience_aggregation' => false,
        'count' => 25, 'page' => 0,
        'sort' => 'name', 'sort_order' => 'asc'))
    {
        $inf = new Influencers();

        // Sanitize default values
        $p['is_tag_prefix'] = $inf->convertBool($p, 'is_tag_prefix');
        $p['enable_tags_aggregation'] = $inf->convertBool($p, 'enable_tags_aggregation');
        $p['enable_country_aggregation'] = $inf->convertBool($p, 'enable_country_aggregation');
        $p['enable_audience_aggregation'] = $inf->convertBool($p, 'enable_audience_aggregation');

        $p = $inf->addCustomerKey($p);

        // support for multi params
        if (isset($p['influencers'])) {
            $p['influencers'] = is_array($p['influencers']) ?
                implode(',', $p['influencers']) : $p['influencers'];
        }
        if (isset($p['tags'])) {
            $p['tags'] = is_array($p['tags']) ?
                implode(',', $p['tags']) : $p['tags'];
        }
        if (isset($p['tags_exclusive'])) {
            $p['tags_exclusive'] = is_array($p['tags_exclusive']) ?
                implode(',', $p['tags_exclusive']) : $p['tags_exclusive'];
        }
        if (isset($p['emails'])) {
            $p['emails'] = is_array($p['emails']) ?
                implode(',', $p['emails']) : $p['emails'];
        }
        return $inf->post(TraackrApi::$apiBaseUrl . 'influencers/lookup', $p);
    }

    /**
     * Search for influencers based on audience criteria
     *
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function searchAudience($p = [
        'audience' => '{"network": "twitter"}',
        'influencers' => null,
        'tags' => null,
        'tags_exclusive' => null,
        'is_tag_prefix' => false,
        'count' => 25])
    {
        $inf = new Influencers();

        // Sanitize default values
        $p['is_tag_prefix'] = $inf->convertBool($p, 'is_tag_prefix');

        $p = $inf->addCustomerKey($p);
        $inf->checkRequiredParams($p, ['audience']);

        if (isset($p['influencers'])) {
            $p['influencers'] = is_array($p['influencers']) ?
                implode(',', $p['influencers']) : $p['influencers'];
        }

        if (isset($p['tags'])) {
            $p['tags'] = is_array($p['tags']) ? implode(',', $p['tags']) : $p['tags'];
        }

        if (isset($p['tags_exclusive'])) {
            $p['tags_exclusive'] = is_array($p['tags_exclusive']) ?
                implode(',', $p['tags_exclusive']) : $p['tags_exclusive'];
        }

        return $inf->post(TraackrApi::$apiBaseUrl . 'influencers/search/audience', $p);
    }

    /**
     * Search for influencers
     *
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function search($p = array(
        'is_tag_prefix' => false,
        'gender' => 'all',
        'type' => 'person',
        'lang' => 'all',
        'enable_audience_aggregation' => false,
        'enable_country_aggregation' => false,
        'count' => 25))
    {
        $inf = new Influencers();

        // Sanitize default values
        $p['is_tag_prefix'] = $inf->convertBool($p, 'is_tag_prefix');
        $p['enable_audience_aggregation'] = $inf->convertBool($p, 'enable_audience_aggregation');
        $p['enable_country_aggregation'] = $inf->convertBool($p, 'enable_country_aggregation');

        $p = $inf->addCustomerKey($p);
        $inf->checkRequiredParams($p, array('keywords'));

        // support for multi params
        $p['keywords'] = is_array($p['keywords']) ?
            implode(',', $p['keywords']) : $p['keywords'];
        if (isset($p['influencers'])) {
            $p['influencers'] = is_array($p['influencers']) ?
                implode(',', $p['influencers']) : $p['influencers'];
        }
        if (isset($p['tags'])) {
            $p['tags'] = is_array($p['tags']) ?
                implode(',', $p['tags']) : $p['tags'];
        }
        if (isset($p['tags_exclusive'])) {
            $p['tags_exclusive'] = is_array($p['tags_exclusive']) ?
                implode(',', $p['tags_exclusive']) : $p['tags_exclusive'];
        }
        if (isset($p['exclusion_keywords'])) {
            $p['exclusion_keywords'] = is_array($p['exclusion_keywords']) ?
                implode(',', $p['exclusion_keywords']) : $p['exclusion_keywords'];
        }
        if (isset($p['root_urls_inclusive'])) {
            $p['root_urls_inclusive'] = is_array($p['root_urls_inclusive']) ?
                implode(',', $p['root_urls_inclusive']) : $p['root_urls_inclusive'];
        }
        if (isset($p['root_urls_exclusive'])) {
            $p['root_urls_exclusive'] = is_array($p['root_urls_exclusive']) ?
                implode(',', $p['root_urls_exclusive']) : $p['root_urls_exclusive'];
        }
        if (isset($p['emails'])) {
            $p['emails'] = is_array($p['emails']) ?
                implode(',', $p['emails']) : $p['emails'];
        }
        return $inf->post(TraackrApi::$apiBaseUrl . 'influencers/search', $p);
    }

    /**
     * Add a channel URL to an influencer
     *
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function channelsAdd($p = [])
    {
        $inf = new Influencers();

        $inf->checkRequiredParams($p, ['influencer', 'url']);

        return $inf->post(TraackrApi::$apiBaseUrl . 'influencers/channels/add', $p);
    }

    /**
     * Report an influencer
     *
     * @param array $p
     * @return bool|mixed
     * @throws MissingParameterException
     */
    public static function report($p = [])
    {
        $inf = new Influencers();

        $inf->checkRequiredParams($p, ['influencer', 'url']);

        return $inf->post(TraackrApi::$apiBaseUrl . 'influencers/report', $p);
    }
}
