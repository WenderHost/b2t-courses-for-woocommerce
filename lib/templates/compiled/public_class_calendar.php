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
  span.days{
    display: block;
    font-weight: bold;
  }
  .header div[class^=col]{
    background: #009ee0;
    color: #fff;
    padding: 16px;
    font-weight: bold;
  }
  .class-calendar{
    font-family: FuturaPT, Futura, sans-serif;
  }
  .class-calendar .course-title{
    font-size: 18px;
    font-weight: bold;
  }
  .class-calendar div[class^=col]{
    padding: 16px;
  }
  .class-calendar a.button{
    background-color: #009ee0;
    color: #fff;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
    padding: 8px 12px;
  }
  .class-calendar a.button:hover{
    text-decoration: none;
    background-color: #fff;
    color: #009ee0;
    font-weight: bold;
  }
  .class-calendar .row.alt{
    background-color: #dedee0;
  }
  .class-calendar .mobile-row{
    border-bottom: 1px solid #999;
    padding: 20px;
  }
  .class-calendar .mobile-row div{
    margin: .25em 0;
  }
  @media screen and (min-width: 1024px){
    .hide-md{
      display: none;
    }
  }
  @media screen and (max-width: 1024px){
    .hide-sm{
      display: none !important;
    }
  }
</style>
<div class="class-calendar">
  <div class="row header hide-sm">
    <div class="col-md-2">Dates</div>
    <div class="col-md-3">Course</div>
    <div class="col-md">Location</div>
    <div class="col-md">Time</div>
    <div class="col-md">Price</div>
    <div class="col-md">&nbsp;</div>
  </div>
'.LR::sec($cx, (($inary && isset($in['classes'])) ? $in['classes'] : null), null, $in, true, function($cx, $in) {$inary=is_array($in);return '  <div class="row '.htmlspecialchars((string)(($inary && isset($in['css_classes'])) ? $in['css_classes'] : null), ENT_QUOTES, 'UTF-8').' hide-sm">
    <div class="col-md-2"><span class="days">'.(($inary && isset($in['days'])) ? $in['days'] : null).'</span>'.htmlspecialchars((string)(($inary && isset($in['year'])) ? $in['year'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="col-md-3"><a href="'.htmlspecialchars((string)(($inary && isset($in['course_url'])) ? $in['course_url'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)(($inary && isset($in['course_title'])) ? $in['course_title'] : null), ENT_QUOTES, 'UTF-8').'</a></div>
    <div class="col-md">'.htmlspecialchars((string)(($inary && isset($in['location'])) ? $in['location'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="col-md">'.htmlspecialchars((string)(($inary && isset($in['times'])) ? $in['times'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="col-md">'.(($inary && isset($in['price'])) ? $in['price'] : null).'</div>
    <div class="col-md" style="text-align: center;"><a class="button" href="'.htmlspecialchars((string)(($inary && isset($in['register_url'])) ? $in['register_url'] : null), ENT_QUOTES, 'UTF-8').'">Register</a></div>
  </div>
  <div class="'.htmlspecialchars((string)(($inary && isset($in['css_classes'])) ? $in['css_classes'] : null), ENT_QUOTES, 'UTF-8').' hide-md mobile-row">
    <div class="course-title"><a href="'.htmlspecialchars((string)(($inary && isset($in['course_url'])) ? $in['course_url'] : null), ENT_QUOTES, 'UTF-8').'">'.htmlspecialchars((string)(($inary && isset($in['course_title'])) ? $in['course_title'] : null), ENT_QUOTES, 'UTF-8').'</a></div>
    <div class="">'.(($inary && isset($in['days'])) ? $in['days'] : null).', '.htmlspecialchars((string)(($inary && isset($in['year'])) ? $in['year'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="">'.htmlspecialchars((string)(($inary && isset($in['location'])) ? $in['location'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="">'.htmlspecialchars((string)(($inary && isset($in['times'])) ? $in['times'] : null), ENT_QUOTES, 'UTF-8').'</div>
    <div class="">'.(($inary && isset($in['price'])) ? $in['price'] : null).'</div>
    <div class=""><a class="button" href="'.htmlspecialchars((string)(($inary && isset($in['register_url'])) ? $in['register_url'] : null), ENT_QUOTES, 'UTF-8').'">Register</a></div>
  </div>
';}).'</div>';
};
?>