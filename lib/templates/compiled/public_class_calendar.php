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
    return '<div class="class-calendar-filter">
  <h4>Location:</h4>
  <ul class="filter-link-group"></ul>
</div>
<div class="legend"><span class="check">'.htmlspecialchars((string)((isset($in['labels']['legend']) && is_array($in['labels']['legend']) && isset($in['labels']['legend']['confirmed'])) ? $in['labels']['legend']['confirmed'] : null), ENT_QUOTES, 'UTF-8').'</span></div>
<div class="class-calendar">
  <div class="row header hide-sm">
    <div class="col confirmed"><div class="check"></div></div>
    <div class="col-md date"><span>'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['dates'])) ? $in['labels']['dates'] : null), ENT_QUOTES, 'UTF-8').'</span></div>
    <div class="col-md-3 course">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['course'])) ? $in['labels']['course'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="col-md-2 location">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['location'])) ? $in['labels']['location'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="col-md-2 time">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['course_length'])) ? $in['labels']['course_length'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <!--<div class="col-md duration">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['duration'])) ? $in['labels']['duration'] : null), ENT_QUOTES, 'UTF-8').'</div>-->
    <div class="col-md price">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['price'])) ? $in['labels']['price'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <!--<div class="col-md lang">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['language'])) ? $in['labels']['language'] : null), ENT_QUOTES, 'UTF-8').'</div>-->
    <div class="col-md actions">&nbsp;</div>
  </div>
'.LR::sec($cx, (($inary && isset($in['classes'])) ? $in['classes'] : null), null, $in, true, function($cx, $in) {$inary=is_array($in);return '  <div class="row '.htmlspecialchars((string)(($inary && isset($in['css_classes'])) ? $in['css_classes'] : null), ENT_QUOTES, 'UTF-8').' hide-sm desktop-row location-'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['slug'])) ? $in['location']['slug'] : null), ENT_QUOTES, 'UTF-8').'">
    <div class="col confirmed">'.((LR::ifvar($cx, (($inary && isset($in['confirmed'])) ? $in['confirmed'] : null), false)) ? '<div class="check"></div>' : '').'</div>
    <div class="col-md date"><span class="days">'.(($inary && isset($in['days'])) ? $in['days'] : null).'</span>'.htmlspecialchars((string)(($inary && isset($in['year'])) ? $in['year'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="col-md-3 course"><a href="'.htmlspecialchars((string)(($inary && isset($in['course_url'])) ? $in['course_url'] : null), ENT_QUOTES, 'UTF-8').'">'.(($inary && isset($in['course_title'])) ? $in['course_title'] : null).'</a></div>
    <div class="col-md-2 location">
      <img src="'.htmlspecialchars((string)((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['plugin_dir'])) ? $cx['scopes'][count($cx['scopes'])-1]['plugin_dir'] : null), ENT_QUOTES, 'UTF-8').'lib/img/class_'.((LR::ifvar($cx, (($inary && isset($in['virtual'])) ? $in['virtual'] : null), false)) ? 'virtual' : 'onsite').'.svg" />
      <a class="location-name" data-location="'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['slug'])) ? $in['location']['slug'] : null), ENT_QUOTES, 'UTF-8').'"  href="#location-'.htmlspecialchars((string)(($inary && isset($in['ID'])) ? $in['ID'] : null), ENT_QUOTES, 'UTF-8').'-'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['id'])) ? $in['location']['id'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['name'])) ? $in['location']['name'] : null), ENT_QUOTES, 'UTF-8').'</a>
      <div class="location-description" id="location-'.htmlspecialchars((string)(($inary && isset($in['ID'])) ? $in['ID'] : null), ENT_QUOTES, 'UTF-8').'-'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['id'])) ? $in['location']['id'] : null), ENT_QUOTES, 'UTF-8').'">
        '.((isset($in['location']) && is_array($in['location']) && isset($in['location']['description'])) ? $in['location']['description'] : null).'
      </div>
    </div>
    <div class="col-md-2 time">'.((LR::ifvar($cx, (($inary && isset($in['times'])) ? $in['times'] : null), false)) ? ''.htmlspecialchars((string)(($inary && isset($in['times'])) ? $in['times'] : null), ENT_QUOTES, 'UTF-8').'<br>' : '').''.((LR::ifvar($cx, (($inary && isset($in['duration'])) ? $in['duration'] : null), false)) ? ''.(($inary && isset($in['duration'])) ? $in['duration'] : null).'' : '').'</div>
    <!--<div class="col-md duration">'.(($inary && isset($in['duration'])) ? $in['duration'] : null).'</div>-->
    <div class="col-md price'.((LR::ifvar($cx, ((isset($in['pricing']) && is_array($in['pricing']) && isset($in['pricing']['on_sale'])) ? $in['pricing']['on_sale'] : null), false)) ? ' onsale' : '').'">
'.((LR::ifvar($cx, ((isset($in['pricing']) && is_array($in['pricing']) && isset($in['pricing']['on_sale'])) ? $in['pricing']['on_sale'] : null), false)) ? '        <s>'.htmlspecialchars((string)((isset($in['pricing']['formatted']) && is_array($in['pricing']['formatted']) && isset($in['pricing']['formatted']['regular_price'])) ? $in['pricing']['formatted']['regular_price'] : null), ENT_QUOTES, 'UTF-8').'</s>'.htmlspecialchars((string)((isset($in['pricing']['formatted']) && is_array($in['pricing']['formatted']) && isset($in['pricing']['formatted']['current_price'])) ? $in['pricing']['formatted']['current_price'] : null), ENT_QUOTES, 'UTF-8').'
' : '        '.htmlspecialchars((string)((isset($in['pricing']['formatted']) && is_array($in['pricing']['formatted']) && isset($in['pricing']['formatted']['current_price'])) ? $in['pricing']['formatted']['current_price'] : null), ENT_QUOTES, 'UTF-8').'
').'    </div>
    <!--<div class="col-md lang">'.htmlspecialchars((string)(($inary && isset($in['lang'])) ? $in['lang'] : null), ENT_QUOTES, 'UTF-8').'</div>-->
    <div class="col-md actions" style="text-align: center; display: flex; flex-direction: column; justify-content: center;"><a class="button" href="'.htmlspecialchars((string)(($inary && isset($in['register_url'])) ? $in['register_url'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]['labels']) && isset($cx['scopes'][count($cx['scopes'])-1]['labels']['register'])) ? $cx['scopes'][count($cx['scopes'])-1]['labels']['register'] : null), ENT_QUOTES, 'UTF-8').'</a></div>
  </div>
';}).''.LR::sec($cx, (($inary && isset($in['classes'])) ? $in['classes'] : null), null, $in, true, function($cx, $in) {$inary=is_array($in);return '  <div class="'.htmlspecialchars((string)(($inary && isset($in['css_classes'])) ? $in['css_classes'] : null), ENT_QUOTES, 'UTF-8').' hide-md mobile-row location-'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['slug'])) ? $in['location']['slug'] : null), ENT_QUOTES, 'UTF-8').'">
    <div class="course-title"><a href="'.htmlspecialchars((string)(($inary && isset($in['course_url'])) ? $in['course_url'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)(($inary && isset($in['course_title'])) ? $in['course_title'] : null), ENT_QUOTES, 'UTF-8').'</a></div>
    <div class="course-dates">'.((LR::ifvar($cx, (($inary && isset($in['confirmed'])) ? $in['confirmed'] : null), false)) ? '<div class="check"></div>' : '').' <div>'.(($inary && isset($in['days'])) ? $in['days'] : null).', '.htmlspecialchars((string)(($inary && isset($in['year'])) ? $in['year'] : null), ENT_QUOTES, 'UTF-8').'</div></div>
    <div class="location">
      <img src="'.htmlspecialchars((string)((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]) && isset($cx['scopes'][count($cx['scopes'])-1]['plugin_dir'])) ? $cx['scopes'][count($cx['scopes'])-1]['plugin_dir'] : null), ENT_QUOTES, 'UTF-8').'lib/img/class_'.((LR::ifvar($cx, (($inary && isset($in['virtual'])) ? $in['virtual'] : null), false)) ? 'virtual' : 'onsite').'.svg" />
      <a class="location-name" data-location="'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['slug'])) ? $in['location']['slug'] : null), ENT_QUOTES, 'UTF-8').'"  href="#location-'.htmlspecialchars((string)(($inary && isset($in['ID'])) ? $in['ID'] : null), ENT_QUOTES, 'UTF-8').'-'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['id'])) ? $in['location']['id'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['name'])) ? $in['location']['name'] : null), ENT_QUOTES, 'UTF-8').'</a>
      <div class="location-description" id="location-'.htmlspecialchars((string)(($inary && isset($in['ID'])) ? $in['ID'] : null), ENT_QUOTES, 'UTF-8').'-'.htmlspecialchars((string)((isset($in['location']) && is_array($in['location']) && isset($in['location']['id'])) ? $in['location']['id'] : null), ENT_QUOTES, 'UTF-8').'">
        '.((isset($in['location']) && is_array($in['location']) && isset($in['location']['description'])) ? $in['location']['description'] : null).'
      </div>
    </div>
    <div class="">'.htmlspecialchars((string)(($inary && isset($in['times'])) ? $in['times'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="">'.(($inary && isset($in['duration'])) ? $in['duration'] : null).'</div>
    <div class="price'.((LR::ifvar($cx, ((isset($in['pricing']) && is_array($in['pricing']) && isset($in['pricing']['on_sale'])) ? $in['pricing']['on_sale'] : null), false)) ? ' onsale' : '').'">
'.((LR::ifvar($cx, ((isset($in['pricing']) && is_array($in['pricing']) && isset($in['pricing']['on_sale'])) ? $in['pricing']['on_sale'] : null), false)) ? '        <s>'.htmlspecialchars((string)((isset($in['pricing']) && is_array($in['pricing']) && isset($in['pricing']['regular_price'])) ? $in['pricing']['regular_price'] : null), ENT_QUOTES, 'UTF-8').'</s>'.htmlspecialchars((string)((isset($in['pricing']) && is_array($in['pricing']) && isset($in['pricing']['current_price'])) ? $in['pricing']['current_price'] : null), ENT_QUOTES, 'UTF-8').'
' : '        '.htmlspecialchars((string)((isset($in['pricing']) && is_array($in['pricing']) && isset($in['pricing']['current_price'])) ? $in['pricing']['current_price'] : null), ENT_QUOTES, 'UTF-8').'
').'    </div>
    <div class="lang">'.htmlspecialchars((string)(($inary && isset($in['lang'])) ? $in['lang'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="actions"><a class="button" href="'.htmlspecialchars((string)(($inary && isset($in['register_url'])) ? $in['register_url'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)((isset($cx['scopes'][count($cx['scopes'])-1]) && is_array($cx['scopes'][count($cx['scopes'])-1]['labels']) && isset($cx['scopes'][count($cx['scopes'])-1]['labels']['register'])) ? $cx['scopes'][count($cx['scopes'])-1]['labels']['register'] : null), ENT_QUOTES, 'UTF-8').'</a></div>
  </div>
';}).'</div>';
};
?>