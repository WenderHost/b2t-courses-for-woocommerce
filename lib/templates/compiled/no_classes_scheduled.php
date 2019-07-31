<?php
use \LightnCandy\SafeString as SafeString;use \LightnCandy\Runtime as LR;return function ($in = null, $options = null) {
    $helpers = array();
    $partials = array();
    $cx = array(
        'flags' => array(
            'jstrue' => false,
            'jsobj' => false,
            'jslen' => false,
            'spvar' => true,
            'prop' => false,
            'method' => false,
            'lambda' => false,
            'mustlok' => false,
            'mustlam' => false,
            'mustsec' => false,
            'echo' => false,
            'partnc' => false,
            'knohlp' => false,
            'debug' => isset($options['debug']) ? $options['debug'] : 1,
        ),
        'constants' => array(),
        'helpers' => isset($options['helpers']) ? array_merge($helpers, $options['helpers']) : $helpers,
        'partials' => isset($options['partials']) ? array_merge($partials, $options['partials']) : $partials,
        'scopes' => array(),
        'sp_vars' => isset($options['data']) ? array_merge(array('root' => $in), $options['data']) : array('root' => $in),
        'blparam' => array(),
        'partialid' => 0,
        'runtime' => '\LightnCandy\Runtime',
    );
    
    $inary=is_array($in);
    return '<div class="public-classes widget">
  <h3 class="widget-title" style="margin-bottom: 20px;">Public Classes</h3>

  <p style="font-size: 18px; font-family: futura-pt, Futura, sans-serif;">Currently, we don\'t have any public sessions of this course scheduled. Please <a href="mailto:info@netmind.net?subject='.htmlspecialchars((string)(($inary && isset($in['title'])) ? $in['title'] : null), ENT_QUOTES, 'UTF-8').' - Public Class Inquiry">let us know</a> if you are interested in adding a session.</p>
  <a href="/course-calendar/" class="button" style="font-size: 10px; padding: 10px 12px; background-color: #009ee0; color: #fff; border-radius: 0;">See Public Class Schedule</a>
</div>
';
};
?>