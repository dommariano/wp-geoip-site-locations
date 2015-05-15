jQuery( document ).ready( function() {
  ( function( $ ) {

    geoipsl_triggerSwitcher = function() {
      if ( geoipsltracker.triggerElement ) {
        $switcher = $( geoipsltracker.triggerElement );

        if ( $switcher.length ) {
          $switcher = $switcher.eq(0);
          $switcher[0].click();
        }
      }
    }

    geoipsl_getLocation = function() {
      if ( geoPosition.init() ) {
        geoPosition.getCurrentPosition( geoipsl_onGeoSuccess, geoipsl_onGeoError, {
          enableHighAccuracy: ( 1 == parseInt( geoipsltracker.enableHighAccuracy ) ? true : false ),
          maximumAge: 3600000,
        } );
      } else {
        geoipsl_triggerSwitcher();
      }
    }

    geoipsl_onGeoSuccess = function( position ) {
      $.ajax( {
        url: geoipsltracker.ajaxurl,
        type: 'POST',
        data: {
          action: 'ajax_redirect_to_geoip_subsite',
          lat_from: position.coords.latitude,
          lang_from: position.coords.longitude
        },
        success: function( result ) {
          result = $.parseJSON( result );

          if ( 1 == result.length ) {
            cur_link  = window.location.href;
            cur_link  = cur_link.replace( /^http(s?):\/\//gi, '' );
            cur_link  = cur_link.replace( /(\/*)$/gi, '' );
            dest_link = result[0][1];
            dest_link = dest_link.replace( /^http(s?):\/\//gi, '' );
            dest_link = dest_link.replace( /(\/*)$/gi, '' );

            if ( cur_link != dest_link ) {
              window.location.replace( result[0][1] );
            }
          } else {
            geoipsl_triggerSwitcher();
          }
        },
        complete: function() {
        },
        error: function( jqXHR, textStatus, errorThrown ) {
        }
      } );
    }

    geoipsl_onGeoError = function( err ) {
      geoipsl_triggerSwitcher();
    }

    geoipsl_getLocation();

  } ) ( jQuery );
} );
