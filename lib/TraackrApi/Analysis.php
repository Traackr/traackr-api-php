<?php

namespace Traackr;

class Analysis extends TraackrApiObject {

    public static function toplinks($p = array('min_linkbacks' => 10, 'count' => 5))
    {
        $analysis = new Analysis();
        $p = $analysis->addCustomerKey($p);

        // support for multi params
        if ( isset($p['influencers']) ) {
            $p['influencers'] = is_array($p['influencers']) ?
                implode(',', $p['influencers']) : $p['influencers'];
        }
        if ( isset($p['tags']) ) {
            $p['tags'] = is_array($p['tags']) ?
                implode(',', $p['tags']) : $p['tags'];
        }

        return $analysis->get(TraackrApi::$apiBaseUrl.'analysis/toplinks', $p);
    }

    public static function keywords($p = array())
    {
        $analysis = new Analysis();
        $p = $analysis->addCustomerKey($p);

        return $analysis->post(TraackrApi::$apiBaseUrl.'analysis/keywords', $p, true);
    }
}