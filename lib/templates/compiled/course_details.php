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
    return '<style>
.certification-links{
  list-style: none;
  padding: 0;
}
</style>
<div class="widget course-details">
  <h3 class="widget-title">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['course_details'])) ? $in['labels']['course_details'] : null), ENT_QUOTES, 'UTF-8').'</h3>
'.((LR::ifvar($cx, (($inary && isset($in['reference'])) ? $in['reference'] : null), false)) ? '  <div class="detail">
    <h4>'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['reference'])) ? $in['labels']['reference'] : null), ENT_QUOTES, 'UTF-8').'</h4>
    '.htmlspecialchars((string)(($inary && isset($in['reference'])) ? $in['reference'] : null), ENT_QUOTES, 'UTF-8').'
  </div>
' : '').''.((LR::ifvar($cx, (($inary && isset($in['duration'])) ? $in['duration'] : null), false)) ? '  <div class="detail">
    <h4>'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['duration'])) ? $in['labels']['duration'] : null), ENT_QUOTES, 'UTF-8').'</h4>
    '.htmlspecialchars((string)(($inary && isset($in['duration'])) ? $in['duration'] : null), ENT_QUOTES, 'UTF-8').'
  </div>
' : '').''.((LR::ifvar($cx, (($inary && isset($in['delivery_mode'])) ? $in['delivery_mode'] : null), false)) ? '  <div class="detail">
    <h4>'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['delivery_mode'])) ? $in['labels']['delivery_mode'] : null), ENT_QUOTES, 'UTF-8').'</h4>
    '.htmlspecialchars((string)(($inary && isset($in['delivery_mode'])) ? $in['delivery_mode'] : null), ENT_QUOTES, 'UTF-8').'
  </div>
' : '').''.((LR::ifvar($cx, (($inary && isset($in['certification_links'])) ? $in['certification_links'] : null), false)) ? '  <div class="detail">
    <h4>'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['certification'])) ? $in['labels']['certification'] : null), ENT_QUOTES, 'UTF-8').'</h4>
    <ul class="certification-links">
'.LR::sec($cx, (($inary && isset($in['certification_links'])) ? $in['certification_links'] : null), null, $in, true, function($cx, $in) {$inary=is_array($in);return '      <li><a href="'.htmlspecialchars((string)(($inary && isset($in['link'])) ? $in['link'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)(($inary && isset($in['text'])) ? $in['text'] : null), ENT_QUOTES, 'UTF-8').'</a></li>
';}).'    </ul>
  </div>
' : '').'  <div class="buttons">
    <a href="#elementor-action%3Aaction%3Dpopup%3Aopen%26settings%3DeyJpZCI6IjE0OCIsInRvZ2dsZSI6ZmFsc2V9" class="button">'.htmlspecialchars((string)((isset($in['labels']) && is_array($in['labels']) && isset($in['labels']['request_info'])) ? $in['labels']['request_info'] : null), ENT_QUOTES, 'UTF-8').'</a>
    '.((LR::ifvar($cx, (($inary && isset($in['print_version'])) ? $in['print_version'] : null), false)) ? '<a href="'.htmlspecialchars((string)(($inary && isset($in['print_version'])) ? $in['print_version'] : null), ENT_QUOTES, 'UTF-8').'" class="print" target="_blank">Print</a>' : '').'
  </div>
</div>';
};
?>