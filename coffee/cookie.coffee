$ = jQuery.noConflict()

$( document ).ready ->

  supports_html5_storage = ->
    try
      return 'localStorage' in window and window['localStorage'] not null
    catch e
      return false

  createCookie = ( name, value, days, domain ) ->
    if days
      date = new Date()
      date.setTime date.getTime() + ( days*24*60*60*1000 )
      expires = "; expires=" + date.toGMTString()
    else
      expires = ""

    document.cookie = name+"="+value+expires+"; path=/; domain="+domain

  readCookie = ( name ) ->
    nameEQ = name + "="
    ca = document.cookie.split(';')

    i = 0
    while i < ca.length
      c = ca[i]

      while c.charAt(0) == ' '
        c = c.substring 1, c.length

      if c.indexOf(nameEQ) == 0
        return c.substring nameEQ.length, c.length

      i++

    return null

  eraseCookie = ( name, domain ) ->
    createCookie name, "", -1, domain

  $rememberForm = $ 'input[name="geoipsl-remember-me"]'
  $geoipslLinks = $ '[href][data-geoipsl-track]'
  domain = if window.geoipsltracker.currentSite then window.geoipsltracker.currentSite else ''
  domain = domain.trim()
  domain = domain.replace /^https?:\/\//g, ''
  domain = domain.replace /\/$/g, ''
  domain = ".#{domain}"

  cookieData = $.parseJSON unescape readCookie 'wp_geoipsl_tracker'

  if cookieData && cookieData.remember
    $rememberForm.prop( 'checked', true )

    if $rememberForm.parent().hasClass 'prettycheckbox'
      $rememberForm
        .next().addClass( 'checked' )

  if $rememberForm.length
    $rememberForm.on 'change', ( evt ) ->
      if not $(@).is ':checked'
        eraseCookie 'wp_geoipsl_tracker', domain

  $geoipslLinks.on 'click', ( evt ) ->
    if ( $geoipslLinks.length and not $rememberForm.length ) or ( $geoipslLinks.length and $rememberForm.length and $rememberForm.is ':checked' )
      evt.preventDefault()

      href = $(@).attr 'href'
      remember = if $rememberForm.is ':checked' then 1 else 0

      wp_geoipsl =
        href: href
        remember: remember

      wp_geoipsl = JSON.stringify wp_geoipsl

      eraseCookie 'wp_geoipsl_tracker'
      createCookie 'wp_geoipsl_tracker', wp_geoipsl, 30, domain

      window.location = href
