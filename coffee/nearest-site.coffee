$ = jQuery.noConflict()

$( document ).ready ->

  geoipsl_getLocation = ->
    if geoPosition.init()
      geoPosition.getCurrentPosition geoipsl_onGeoSuccess, geoipsl_onGeoError,
        maximumAge: 3600000
    return

  geoipsl_onGeoSuccess = (position) ->
    $.ajax
      url: geoipsltracker.ajaxurl
      type: 'POST'
      data:
        action: 'ajax_redirect_to_geoip_subsite'
        lat_from: position.coords.latitude
        lang_from: position.coords.longitude
      success: (result) ->

        result = $.parseJSON(result)

        if 1 == result.length
          cur_link = window.location.href
          cur_link = cur_link.replace(/^http(s?):\/\//gi, '')
          cur_link = cur_link.replace(/(\/*)$/gi, '')
          dest_link = result[0][1]
          dest_link = dest_link.replace(/^http(s?):\/\//gi, '')
          uest_link = dest_link.replace(/(\/*)$/gi, '')

          $('.geoipsl-closest-site-link').attr( 'href', result[0][1] )

        return
      complete: ->
      error: (jqXHR, textStatus, errorThrown) ->
    return

  geoipsl_onGeoError = (err) ->
    console.log 'Cannot find location.'
    return

  geoipsl_getLocation()
