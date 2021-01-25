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
  <h3 class="widget-title" style="margin-bottom: 20px;">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['widget_title'])) ? $in['labels']['widget_title'] : null), ENT_QUOTES, 'UTF-8').'</h3>
  <p>'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['no_public_classes_message'])) ? $in['labels']['no_public_classes_message'] : null), ENT_QUOTES, 'UTF-8').'</p>
  <a href="/course-calendar/" class="button">See Public Class Schedule</a>
</div>
';
};
?>