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
  <h3 class="widget-title">Public Classes</h3>

'.LR::sec($cx, (($inary && isset($in['classes'])) ? $in['classes'] : null), null, $in, true, function($cx, $in) {$inary=is_array($in);return '    <div class="row">
      <div class="details"><span class="date">'.htmlspecialchars((string)(($inary && isset($in['class_dates'])) ? $in['class_dates'] : null), ENT_QUOTES, 'UTF-8').'</span><br/>'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['name'])) ? $in['location']['name'] : null), ENT_QUOTES, 'UTF-8').'</div>
      <div class="register-link"><a class="button" href="'.htmlspecialchars((string)(($inary && isset($in['register_link'])) ? $in['register_link'] : null), ENT_QUOTES, 'UTF-8').'">Register</a></div>
    </div>
';}).'</div>
';
};
?>