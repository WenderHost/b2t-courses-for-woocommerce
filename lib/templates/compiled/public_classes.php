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
  <h3 class="widget-title">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['widget_title'])) ? $in['labels']['widget_title'] : null), ENT_QUOTES, 'UTF-8').'</h3>

'.LR::sec($cx, (($inary && isset($in['classes'])) ? $in['classes'] : null), null, $in, true, function($cx, $in) {$inary=is_array($in);return '    <div class="row">
      <div class="icon"><img src="'.htmlspecialchars((string)((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['plugin_dir'])) ? $cx['scopes'][count($cx['scopes'])-1]['plugin_dir'] : null), ENT_QUOTES, 'UTF-8').'lib/img/class_'.((LR::ifvar($cx, (($inary && isset($in['virtual'])) ? $in['virtual'] : null), false)) ? 'virtual' : 'onsite').'.svg" /></div>
      <div class="details">
        <div class="date">'.htmlspecialchars((string)(($inary && isset($in['class_dates'])) ? $in['class_dates'] : null), ENT_QUOTES, 'UTF-8').'</div>
        <div class="time">'.htmlspecialchars((string)(($inary && isset($in['times'])) ? $in['times'] : null), ENT_QUOTES, 'UTF-8').' '.((LR::ifvar($cx, (($inary && isset($in['duration'])) ? $in['duration'] : null), false)) ? '| '.htmlspecialchars((string)(($inary && isset($in['duration'])) ? $in['duration'] : null), ENT_QUOTES, 'UTF-8').'' : '').'</div>
        <a class="location-name" href="#location-'.htmlspecialchars((string)(($inary && isset($in['ID'])) ? $in['ID'] : null), ENT_QUOTES, 'UTF-8').'-'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['id'])) ? $in['location']['id'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['name'])) ? $in['location']['name'] : null), ENT_QUOTES, 'UTF-8').'</a>
        <div class="location-description" id="location-'.htmlspecialchars((string)(($inary && isset($in['ID'])) ? $in['ID'] : null), ENT_QUOTES, 'UTF-8').'-'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['id'])) ? $in['location']['id'] : null), ENT_QUOTES, 'UTF-8').'">'.((isset($in['location']) && is_array($in['location']) && isset($in['location']['description'])) ? $in['location']['description'] : null).'</div>
        <div class="register-link"><a class="button" href="'.htmlspecialchars((string)(($inary && isset($in['register_link'])) ? $in['register_link'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]['labels']) && isset($cx['scopes'][count($cx['scopes'])-1]['labels']['button_label'])) ? $cx['scopes'][count($cx['scopes'])-1]['labels']['button_label'] : null), ENT_QUOTES, 'UTF-8').'</a></div>
      </div>
    </div>
';}).'</div>
';
};
?>