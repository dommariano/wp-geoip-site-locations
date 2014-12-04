<form id="supported-locations-filter" action="" method="">
  <input type="hidden" name="page" value="geoip-site-locations/geoip-site-locations.php/">

  <h3>MaxMind Account Info</h3>
  <p>Please supply your MaxMind User ID and License Key in order to use MaxMind Web Services.</p>

  <table class="wp-list-table widefat fixed">
    <tbody>
      <tr>
        <td width="100">User ID</td>
        <td><input type="text" value="" name="maxmind_user_id" placeholder="User ID"></td>
      </tr>

      <tr>
        <td width="100">License Key</td>
        <td><input type="password" value="" name="maxmind_license_key" placeholder="License Key"></td>
      </tr>
    </tbody>
  </table>

  <h3>Google Distance Matrix API</h3>
  <p>You may optionally opt to use the Google Distance Matrix web service when geotargetting based on how far or near the site visitor is relative to an arbitrary point. By default, our geotargetting distance logic will use <b>geodesic</b> calculations instead of actual <b>travelling distance.</b></p>

  <table class="wp-list-table widefat fixed">
    <tbody>
      <tr>
        <td width="180">Client ID</td>
        <td><input type="text" value="" name="maxmind_user_id" placeholder="Client ID"></td>
      </tr>

      <tr>
        <td width="180">Private Cryptographic Key</td>
        <td><input type="password" value="" name="maxmind_license_key" placeholder="Private Cryptographic Key"></td>
      </tr>
    </tbody>
  </table>
</form>