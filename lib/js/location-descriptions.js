/* Location descriptions */
(function($){
  $('.public-classes, .class-calendar').on('click','.location-name',function(e){
    e.preventDefault();
    //var hash = $(this)[0].hash;
    $(this).siblings('.location-description').slideToggle();
  });
})(jQuery);