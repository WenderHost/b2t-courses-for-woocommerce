const calRowLangClass = ( 'en_US' != wpvars.locale )? 'cal_es' : 'cal_en' ;
let pageWidth = jQuery('body').width();
let calendarViewMode = ( 1024 <= pageWidth )? 'desktop' : 'mobile' ;

(function($){

  $('.public-classes, .class-calendar').on('click','.location-name',function(e){
    e.preventDefault();
    $(this).siblings('.location-description').slideToggle();
  });

  // Add zebra striping to rows
  stripeRows();
  $(window).on('resize', function(){
    stripeRows();
  } );

  /* Location Filter */
  initializeLocationFilters();
})(jQuery);

/**
 * Sets up the Calendar Location Filters
 */
function initializeLocationFilters(){
  // Build array of Location Names and Location Slugs
  const locations = [];
  const locationSlugs = [];
  jQuery('.class-calendar .row.desktop-row .location a.location-name').each(function(){
    let currentLocation = jQuery(this).html();
    let currentLocationSlug = jQuery(this).attr('data-location');
    if( -1 === locations.indexOf( currentLocation ) )
      locations.push( currentLocation );
    if( -1 === locationSlugs.indexOf( currentLocationSlug ) )
      locationSlugs.push( currentLocationSlug );
  });

  if( (0 || 1) === locations.length ){
    jQuery('.class-calendar-filter').hide();
    return;
  }

  const calendarHeight = jQuery('.class-calendar').height();
  jQuery('.class-calendar').height(calendarHeight);

  // Add Location filter buttons
  jQuery('.class-calendar-filter ul').append(`<li><a href="*" class="selected">All</a></li>`);
  jQuery(locations).each(function( index ){
    jQuery('.class-calendar-filter ul').append(`<li><a href="` + locationSlugs[index] + `">${this}</a></li>`)
  });

  // Add event listener for Location filter buttons
  const allFilterButtons = jQuery('.filter-link-group a');
  console.log(`🔔 allFilterButtons = `, allFilterButtons);
  jQuery('.class-calendar-filter').on('click','a',function(e){
    jQuery(allFilterButtons).removeClass('selected');
    jQuery(this).addClass('selected');
    e.preventDefault();
    let location = jQuery(this).attr('href');
    console.log(`🔔 Filtering for '${location}'.`);
    jQuery('.row:not(.header)').fadeOut(300);
    let selector = '';
    if(`*` === location){
      selector = `.row.${calendarViewMode}-row.${calRowLangClass}`;
    } else {
      selector = `.row.location-${location}.${calendarViewMode}-row.${calRowLangClass}`;
    }
    jQuery(selector).fadeIn(300);
  });
}

/**
 * Add zebra striping to the calendar rows
 */
function stripeRows(){
  jQuery(`.class-calendar > .row.desktop-row:odd:visible`).addClass('stripe'); // 03/22/2021 (07:01) - original selector was `.class-calendar > .row.${calRowLangClass}.desktop-row:odd:visible`, removing .${calRowLangClass} as this wasn't being applied to all rows resulting in inconsistent zebra striping
}