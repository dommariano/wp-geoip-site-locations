$ = jQuery.noConflict()

$( document ).ready ->

  supports_html5_storage = ->
    try
      return 'localStorage' in window and window['localStorage'] not null
    catch e
      return false

  createCookie = ( name, value, days ) ->
    if days
      date = new Date()
      date.setTime date.getTime() + ( days*24*60*60*1000 )
      expires = "; expires=" + date.toGMTString()
    else
      expires = ""

    document.cookie = name+"="+value+expires+"; path=/"

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

  $persistenceFormCheck = $ 'input[name="geoipsl-persistent-redirect"]'
  blogid                = window.geoipsltracker.blogid
  rememberLastBlogId    = window.geoipsltracker.rememberLastBlogId

  if $persistenceFormCheck.length
    $persistenceFormCheck.on 'change', ( evt ) ->
