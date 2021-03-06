<?php

/**
 * Get the date corresponding to a given day in the current week.
 *
 * For example, if today is Thursday, 11 September 2014, what would be date
 * corrensponding to Monday of today's curent week?
 *
 * @since 0.1.0
 *
 * @param string $day Day of the week, or numeric equivalent where 1 is for Sunday to 7 for Saturday
 * @param int $relative_to_date Date in Unix timestamp format for which to base our calculation
 *                              If provided, this will be used instead of current time
 * @return int Unix Time (seconds)
 */
function geoipsl_get_date_of_day_on_week( $day = "Monday", $relative_to_date = '' ) {
  // Acceptable arguments --- visually match the index in actual calendars we use
  $days = array(
    "Mon"       => 2,
    "Monday"    => 2,
    "Tue"       => 3,
    "Tuesday"   => 3,
    "Wed"       => 4,
    "Wednesday" => 4,
    "Thu"       => 5,
    "Thur"      => 5,
    "Thursday"  => 5,
    "Fri"       => 6,
    "Friday"    => 6,
    "Sat"       => 7,
    "Saturday"  => 7,
    "Sun"       => 1,
    "Sunday"    => 1,
  );

  $current_date = ( is_numeric( $relative_to_date ) && $relative_to_date > 0 ) ? $relative_to_date : time();

  if ( ! in_array( $day, $days ) && ! in_array( $day, array_keys( $days ) ) ) {
    return 0;
  }

  $day = ( is_numeric( $day ) ) ? $day : $days[ $day ];

  // The current day --- ISO-8601 numeric representation of the day of the week
  $current_day = ( 7 == date( 'N', $current_date ) ) ? 1 : 1 + date( 'N', $current_date ); // 7 for Saturday, 1 for Sunday

  // If the day sought is the same day now, just return it
  if ( $day ==  $current_day ) {
    return $current_date; // seconds since January 1 1970 00:00:00 UTC
  }

  // Return the date of the day sought relative to the current day or to a particular date if specified
  if ( $day < $current_day ) {
    return $current_date - ( abs( $current_day - $day ) * 60 * 60 * 24 );
  } else {
    return $current_date + ( abs( $current_day - $day ) * 60 * 60 * 24 );
  }
}

/**
 * Get the date of the next day of the week.
 *
 * For example, if today is Thursday, 11 September 2014, and we want to find out the date of next Friday
 * this function geoipsl_will then return 12 September 2014 in Unix timestamp. However, if today is Saturday,
 * 13 September 2014, then Friday this week has already passed so we return the "next" Friday which is
 * 19 September 2014.
 *
 * @since 0.1.0
 *
 * @uses geoipsl_get_date_of_day_on_week()
 * @param string $day Day of the week, or numeric equivalent where 1 is for Sunday to 7 for Saturday
 * @param int $relative_to_date Date in Unix timestamp format for which to base our calculation
 *                              If provided, this will be used instead of current time
 * @return int Unix Time (seconds)
 */
function geoipsl_get_next_day_of_week( $day = "Monday", $relative_to_date = '' ) {

  $current_date = ( is_numeric( $relative_to_date ) && $relative_to_date > 0 ) ? $relative_to_date : time();

  return ( time() > geoipsl_get_date_of_day_on_week( $day, $relative_to_date ) )
    ? geoipsl_get_date_of_day_on_week( $day, $current_date + ( 7 * 60 * 60 * 24 ) )
    : geoipsl_get_date_of_day_on_week( $day, $relative_to_date );
}

/**
 * Retieve all dates falling on all the weeks of a given calendar for a given year.
 *
 * For example, what are all the dates on the September 2014 calendar? This function geoipsl_will return
 * a 0-indexed array of dates from August 31, 2014 to October 4, 2014. Visually:
 *
 * September 2014
 * S   M   T   W   T   F   Sat
 * 31  1   2   3   4   5   6
 * 7   8   9   10  11  12  13
 * 14  15  16  17  18  19  20
 * 21  22  23  24  25  26  27
 * 28  29  30  1   2   3   4
 *
 * @since 0.1.0
 *
 * @uses geoipsl_get_date_of_day_on_week()
 * @param string $month Full or short month name, or numeric equivalent from 1 to 12
 * @param int $year Four digit year, beginning from 1970
 * @param string $return_format Value of 'use_time_stamp' returns array or UNIX timestamps, 'use_numeric_date' returns numeric date
 * @return array Array of all dates falling on all the weeks of a given calendar for a given year
 */
function geoipsl_get_all_days( $month = "January", $year = 2014, $return_format = 'use_numeric_date' ) {

  $months = array(
    'Jan'       => 1,
    'January'   => 1,
    'Feb'       => 2,
    'February'  => 2,
    'Mar'       => 3,
    'March'     => 3,
    'Apr'       => 4,
    'April'     => 4,
    'May'       => 5,
    'Jun'       => 6,
    'June'      => 6,
    'Jul'       => 7,
    'July'      => 7,
    'Aug'       => 8,
    'August'    => 8,
    'Sept'      => 9,
    'Sep'      => 9,
    'September' => 9,
    'Oct'       => 10,
    'October'   => 10,
    'Nov'       => 11,
    'November'  => 11,
    'Dec'       => 12,
    'December'  => 12,
  );

  $month_long = array(
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
  );


  if ( ! in_array( $month, $months ) && ! in_array( $month, array_keys( $months ) ) ) {
    return 0;
  }

  if ( ! is_numeric( $year ) ) {
    return 0;
  }

  if ( ! in_array( $return_format , array( 'use_time_stamp', 'use_numeric_date' ) ) ) {
    $return_format = 'use_numeric_date';
  }

  // let's make sure we're working on numeric equivalents
  $month           = ( is_numeric( $month ) ) ? $month : $months[ $month ];
  $next_month_text = ( 12 == $month ) ? $month_long['0'] : $month_long[ $month ];
  $month_text      = $month_long[ $month - 1 ];
  $prev_month_text = ( 1 == $month ) ? $month_long['11'] : $month_long[ $month - 2 ];

  // let's make sure we're working with a sensible year
  $year = ( $year > 1970 ) ? $year : 1970;
  $prev_year = $year - 1 ;
  $next_year = $year + 1 ;

  // Number of days present in the current month of the current year
  $numdays_in_month_of_year = cal_days_in_month( CAL_GREGORIAN, date('n', strtotime( "1 $month_text $year" ) ), date('Y', strtotime( "1 $month_text $year" ) ) );

  // September 2014
  // S   M   T   W   T   F   Sat
  // 31  1   2   3   4   5   6
  // 7   8   9   10  11  12  13
  // 14  15  16  17  18  19  20
  // 21  22  23  24  25  26  27
  // 28  29  30  1   2   3   4
  // $start_marker --> 31 September 2014
  // $end_marker   --> 4 October 2014

  $start_marker               = geoipsl_get_date_of_day_on_week( 'Sunday', strtotime( "1 $month_text $year" ) );
  $numdays_in_prev_month      = cal_days_in_month( CAL_GREGORIAN, date( 'n', $start_marker ), date( 'Y', $start_marker ) );

  // if starting date of the week is not 1
  $heading_prev_month_days    = ( date( 'j', $start_marker ) > 1 ) ? $numdays_in_prev_month - date( 'j', $start_marker ) + 1 : 0;

  $end_marker                 = geoipsl_get_date_of_day_on_week( 'Saturday', $start_marker + ( $heading_prev_month_days * 60 * 60 * 24 ) + ( $numdays_in_month_of_year * 60 * 60 * 24 ) );
  $num_cal_days               = 1 + ( $end_marker - $start_marker ) / ( 60 * 60 * 24 );
  $numdays_in_next_month      = cal_days_in_month( CAL_GREGORIAN, date( 'n', $end_marker ), date( 'Y', $end_marker ) );

  // if ending date of the last week of the month is not the last date of the month
  $trailing_next_month_days = ( date( 'j', $end_marker ) < $numdays_in_next_month ) ? date( 'j', $end_marker ) : 0;

  $calendar = array();

  if ( $heading_prev_month_days ) {
    for ( $i = date( 'j', $start_marker ); $i < date( 'j', $start_marker ) + $heading_prev_month_days; $i++ ) {
      $calendar[] = ( 'use_time_stamp' == $return_format ) ? strtotime( "$i $prev_month_text " . ( ( 1 == $month ) ? $year - 1 : $year ) ) : $i;
    }
  }

  for ( $i = 1; $i <= $numdays_in_month_of_year; $i++ ) {
    $calendar[] = ( 'use_time_stamp' == $return_format ) ? strtotime( "$i $month_text $year" ) : $i;
  }

  if ( $trailing_next_month_days ) {
    for ( $i = 1; $i <= $trailing_next_month_days; $i++ ) {
      $calendar[] = ( 'use_time_stamp' == $return_format ) ? strtotime( "$i $next_month_text " . ( ( 12 == $month ) ? $year + 1 : $year ) ) : $i;
    }
  }

  return $calendar;
}

/**
 * Retieve all dates falling on all the weeks of a given calendar for a given year.
 *
 * For example, what are all the dates of all Wednesdays falling on the September 2014 calendar? This function geoipsl_will return
 * a 0-indexed array of the dates of all Wednesdays from August 31, 2014 to October 4, 2014. Visually, it will return the
 * bracketed list below (not include the "day" header):
 *
 * September 2014
 * S   M   T   [ W  ]  T   F   Sat
 * 31  1   2   [ 3  ]   4   5   6
 * 7   8   9   [ 10 ]  11  12  13
 * 14  15  16  [ 17 ]  18  19  20
 * 21  22  23  [ 24 ]  25  26  27
 * 28  29  30  [ 1  ]  2   3   4
 *
 * @since 0.1.0
 *
 * @uses geoipsl_get_date_of_day_on_week()
 * @param string $day Full or short day name, or numeric equivalent from 1 to 7
 * @param string $month Full or short month name, or numeric equivalent from 1 to 12
 * @param int $year Four digit year, beginning from 1970
 * @param string $return_format Value of 'use_time_stamp' returns array or UNIX timestamps, 'use_numeric_date' returns numeric date
 * @return array Array of all dates falling on all the weeks of a given calendar for a given year
 */
function geoipsl_get_these_days( $day = "Monday", $month = "January", $year = 2014, $return_format = 'use_numeric_date' ) {

  $days = array(
    "Mon"       => 2,
    "Monday"    => 2,
    "Tue"       => 3,
    "Tuesday"   => 3,
    "Wed"       => 4,
    "Wednesday" => 4,
    "Thu"       => 5,
    "Thur"      => 5,
    "Thursday"  => 5,
    "Fri"       => 6,
    "Friday"    => 6,
    "Sat"       => 7,
    "Saturday"  => 7,
    "Sun"       => 1,
    "Sunday"    => 1,
  );

  if ( ! in_array( $day, $days ) && ! in_array( $day, array_keys( $days ) ) ) {
    return 0;
  }

  // let's make sure we're working on 0-based numeric equivalents
  $day = ( is_numeric( $day ) ) ? $day : $days[ $day ];
  $day = $day - 1;

  // let's get all the days in this months calendar for the given year
  $calendar = geoipsl_get_all_days( $month, $year, $return_format );

  if ( is_numeric( $calendar ) && 0 == $calendar ) {
    return 0;
  }

  // we will store all the dates falling on the given day
  $dates_by_day = array();

  foreach ( $calendar as $index => $date ) {
    if ( 0 == (  $index - $day ) % 7 ) {
      $dates_by_day[] = $date;
    }
  }

  return $dates_by_day;
}

/**
 * Retrieve the date falling falling on a particular day of the week of the Nth week of a given month on a given year.
 *
 * For example, what date corresponds to the Wednesday of the second week of September 2014? Visually, it will return the
 * intersection of the bracketed lists below:
 *
 *   September 2014
 *   S     M     T   [ W  ]   T      F      Sat
 *   31    1     2   [ 3  ]   4      5      6
 * [ 7 ] [ 8 ] [ 9 ] [ 10 ] [ 11 ] [ 12 ] [ 13 ]
 *   14    15    16  [ 17 ]   18     19     20
 *   21    22    23  [ 24 ]   25     26     27
 *   28    29    30  [ 1  ]   2      3      4
 *
 * @since 0.1.0
 *
 * @uses geoipsl_get_date_of_day_on_week()
 * @param string $day Full or short day name, or numeric equivalent from 1 to 7
 * @param string $week Week number, from 1 to 5
 * @param string $month Full or short month name, or numeric equivalent from 1 to 12
 * @param int $year Four digit year, beginning from 1970
 * @param string $return_format Value of 'use_time_stamp' returns array or UNIX timestamps, 'use_numeric_date' returns numeric date
 * @return array Array of all dates falling on all the weeks of a given calendar for a given year
 */
function geoipsl_get_date_of_day_on_month( $day = "Monday", $week = 1, $month = "January", $year = 2014, $return_format = 'use_numeric_date' ) {
  $weeks = array(
    'week1' => 1,
    'week2' => 2,
    'week3' => 3,
    'week4' => 4,
    'week5' => 5,
  );

  $dates_falling_on_this_day = geoipsl_get_these_days( $day, $month, $year, $return_format );

  if ( is_numeric( $dates_falling_on_this_day ) && 0 == $dates_falling_on_this_day ) {
    return 0;
  }

  if ( in_array( $week, array_keys( $weeks ) ) ) {
    $week = $weeks[ $week ];
  }

  if ( ! is_numeric( $week ) ) {
    return 0;
  }

  $week = ( $week > 0 ) ? $week : 0;
  $week = $week - 1;
  $week = ( $week < count( $dates_falling_on_this_day ) ) ? $week : 0;


  if ( ! isset( $dates_falling_on_this_day[ $week ] ) ) {
    return 0;
  }

  return $dates_falling_on_this_day[ $week ];
}

function geoipsl_get_next_month( $offset = '' ) {

  if ( '' == $offset ) {
    $offset = time();
  }

  if ( is_numeric( $offset ) ) {
    $offset = ( $offset >= 0 ) ? $offset : 0;
  } else {
    $offset = 0;
  }

  // Number of days present in the current month of the current year
  $numdays_cur_month = cal_days_in_month( CAL_GREGORIAN, date( 'n', $offset ), date( 'Y', $offset ) );
  $start_marker = strtotime( "1 " . date( 'F', $offset ) . ' ' . date( 'Y', $offset ) );
  $end_marker = $start_marker + ( $numdays_cur_month * 24 * 60 * 60 ) + 1;

  return array(
    'month'        => date( 'F', $end_marker),
    'year'         => date( 'Y', $end_marker ),
    'first_second' => $end_marker,
  );
}

function geoipsl_get_prev_month( $offset = '' ) {

  if ( '' == $offset ) {
    $offset = time();
  }

  if ( is_numeric( $offset ) ) {
    $offset = ( $offset >= 0 ) ? $offset : 0;
  } else {
    $offset = 0;
  }

  // Number of days present in the current month of the current year
  $numdays_cur_month = cal_days_in_month( CAL_GREGORIAN, date( 'n', $offset ), date( 'Y', $offset ) );
  $start_marker = strtotime( "1 " . date( 'F', $offset ) . ' ' . date( 'Y', $offset ) );
  $end_marker = $start_marker - 1;

  return array(
    'month'        => date( 'F', $end_marker),
    'year'         => date( 'Y', $end_marker ),
    'first_second' => $end_marker,
  );
}

function geoipsl_next_schedule_update_for_geolite2_city() {
  $next_schedule = geoipsl_get_date_of_day_on_month( "Tue", 'week1', date( 'M' ), (int) date( 'Y' ), 'use_time_stamp' );
  if ( time() > $next_schedule ) {
    $next_month = geoipsl_get_next_month();
    $next_schedule = geoipsl_get_date_of_day_on_month( "Tue", 'week1', $next_month['month'], (int) $next_month['year'], 'use_time_stamp' );

    if ( date( 'j', $next_schedule ) > 7 ) {
      $next_schedule = geoipsl_get_date_of_day_on_month( "Tue", 'week2', $next_month['month'], (int) $next_month['year'], 'use_time_stamp' );
    }
  }

  return $next_schedule;
}
