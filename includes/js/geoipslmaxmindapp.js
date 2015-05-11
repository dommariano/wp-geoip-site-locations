jQuery( document ).ready( function() {
  ( function( $ ) {

    var redirect = (function(){

      var triggerSwitcher = function() {
        if ( geoipslapp.triggerElement ) {
          $switcher = $( geoipslapp.triggerElement );

          if ( $switcher.length ) {
            $switcher = $switcher.eq(0);
            $switcher[0].click();
          }
        }
      }

      var onSuccess = function( geoipResponse ) {
        $.ajax( {
          url: geoipslapp.ajaxurl,
          type: 'POST',
          data: {
            action: 'ajax_redirect_to_geoip_subsite',
            lat_from: geoipResponse.location.latitude,
            lang_from: geoipResponse.location.longitude
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

      onError = function( err ) {
        geoipsl_triggerSwitcher();
      }

      return function() {
        geoip2.city( onSuccess, onError );
      }
    }());

    redirect();

  } ) ( jQuery );
} );
