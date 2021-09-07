<?

class ExternalSourcesConstants {

  // external sources types {{{
  const EST_NONE                            = 0;
  const EST_STRAVA                          = 1;
  // }}}

  public static $EXTERNAL_SOURCE_NAMES      = array(
    ExternalSourcesConstants::EST_NONE      => '-',
    ExternalSourcesConstants::EST_STRAVA    => 'Strava',
  );

}

?>
