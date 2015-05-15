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
        c = c.substring(1, c.length)

      if c.indexOf(nameEQ) == 0
        c.substring nameEQ.length, c.length

      i++

    return null

  eraseCookie = ( name ) ->
    createCookie name, "", -1

  $rememberForm = $ 'input[name="geoipsl-remember-me"]'
  $geoipslLinks = $ '[href][data-geoipsl-track]'

  if $rememberForm.length
    $rememberForm.on 'change', ( evt ) ->
      if not $(@).is ':checked'
        eraseCookie 'wp_geoipsl'

  if ( $geoipslLinks.length and not $rememberForm.length ) or ( $geoipslLinks.length and $rememberForm.length and $rememberForm.is ':checked' )
    $geoipslLinks.on 'click', ( evt ) ->
      evt.preventDefault()

      href = $(@).attr 'href'
      remember = if $rememberForm.is ':checked' then 1 else 0

      wp_geoipsl =
        href: href
        remember: remember

      wp_geoipsl = JSON.stringify wp_geoipsl

      domain = if window.geoipsltracker.currentSite then window.geoipsltracker.currentSite else ''
      domain = domain.trim()
      domain = domain.replace /^https?:\/\//g, ''
      domain = domain.replace /\/$/g, ''
      domain = ".#{domain}"

      console.log domain

      eraseCookie 'wp_geoipsl'
      createCookie 'wp_geoipsl', wp_geoipsl, 30, domain

      window.location = href
