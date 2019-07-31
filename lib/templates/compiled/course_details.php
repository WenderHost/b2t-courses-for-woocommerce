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
    return '<style type="text/css">
  .course-details h4{
    font-size: 14px;
    font-weight: bold;
    margin: 0;

  }
  .course-details .detail{
    font-size: 14px;
    font-family: \'futura-pt\', Futura, sans-serif;
    border-bottom: 1px solid #9c9e9f;
    padding-bottom: 10px;
    margin-bottom: 20px;
  }
  .course-details .detail:last-child{
    margin-bottom: 0;
  }
  .course-details a.button{
    background: #009ee0;
    border-radius: 0;
    padding: 10px 12px;
    font-size: 10px;
  }
</style>
<div class="widget course-details">
  <h3 class="widget-title">Course Details</h3>
'.((LR::ifvar($cx, (($inary && isset($in['reference'])) ? $in['reference'] : null), false)) ? '  <div class="detail">
    <h4>Reference</h4>
    '.htmlspecialchars((string)(($inary && isset($in['reference'])) ? $in['reference'] : null), ENT_QUOTES, 'UTF-8').'
  </div>
' : '').''.((LR::ifvar($cx, (($inary && isset($in['duration'])) ? $in['duration'] : null), false)) ? '  <div class="detail">
    <h4>Duration</h4>
    '.htmlspecialchars((string)(($inary && isset($in['duration'])) ? $in['duration'] : null), ENT_QUOTES, 'UTF-8').'
  </div>
' : '').''.((LR::ifvar($cx, (($inary && isset($in['delivery_mode'])) ? $in['delivery_mode'] : null), false)) ? '  <div class="detail">
    <h4>Delivery Mode</h4>
    '.htmlspecialchars((string)(($inary && isset($in['delivery_mode'])) ? $in['delivery_mode'] : null), ENT_QUOTES, 'UTF-8').'
  </div>
' : '').''.((LR::ifvar($cx, (($inary && isset($in['certification'])) ? $in['certification'] : null), false)) ? '  <div class="detail">
    <h4>Certification</h4>
    '.htmlspecialchars((string)(($inary && isset($in['certification'])) ? $in['certification'] : null), ENT_QUOTES, 'UTF-8').'
  </div>
' : '').'  <a href="#elementor-action%3Aaction%3Dpopup%3Aopen%20settings%3DeyJpZCI6IjE0OCIsInRvZ2dsZSI6ZmFsc2V9" class="button">Request Info</a>
</div>';
};
?>