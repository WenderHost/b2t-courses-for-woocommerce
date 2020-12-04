(function($){
  console.log('wpvars.locale =', wpvars.locale);
  $('.public-classes, .class-calendar').on('click','.location-name',function(e){
    e.preventDefault();
    $(this).siblings('.location-description').slideToggle();
  });

  var calRowLangClass = ( 'en_US' != wpvars.locale )? 'cal_es' : 'cal_en' ;

  $(`.class-calendar > .row.${calRowLangClass}.desktop-row:odd:visible`).addClass('stripe');
  $(window).on('resize', function(){
    $(`.class-calendar > .row.${calRowLangClass}.desktop-row:odd:visible`).addClass('stripe');
  } );
})(jQuery);