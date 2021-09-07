<?

class ActivitiesConstants {

  // activities types {{{
  const AT_ANY                                  = -1;
  const AT_RIDE                                 = 1;
  const AT_RUN                                  = 2;
  const AT_VIRTUAL_RIDE                         = 3;
  const AT_NORDIC_SKI                           = 4;
  const AT_WALK                                 = 5;
  const AT_SWIM                                 = 6;
  const AT_ALPINE_SKI                           = 7;
  const AT_BACKCOUNTRY_SKI                      = 8;
  const AT_CANOEING                             = 9;
  const AT_CROSSFIT                             = 10;
  const AT_E_BIKE_RIDE                          = 11;
  const AT_ELLIPTICAL                           = 12;
  const AT_HIKE                                 = 13;
  const AT_ICE_SKATE                            = 14;
  const AT_INLINE_SKATE                         = 15;
  const AT_KAYAKING                             = 16;
  const AT_KITESURF                             = 17;
  const AT_ROCK_CLIMBING                        = 18;
  const AT_ROLLER_SKI                           = 19;
  const AT_ROWING                               = 20;
  const AT_SNOWBOARD                            = 21;
  const AT_SNOWSHOE                             = 22;
  const AT_STAIR_STEPPER                        = 23;
  const AT_STAND_UP_PADDLING                    = 24;
  const AT_SURFING                              = 25;
  const AT_VIRTUAL_RUN                          = 26;
  const AT_WEIGHT_TRAINING                      = 27;
  const AT_WINDSURF                             = 28;
  const AT_WORKOUT                              = 29;
  const AT_YOGA                                 = 30;
  const AT_DANCE                                = 31;
  const AT_SKIALP                               = 32;
  const AT_OTHER                                = 100;

  const DEFAULT_ACTIVITY_TYPE                   = ActivitiesConstants::AT_RIDE;

  public static $ACTIVITIES_INTERNAL_NAMES      = array(
    ActivitiesConstants::AT_ANY                 => 'ANY',
    ActivitiesConstants::AT_RIDE                => 'RIDE',
    ActivitiesConstants::AT_RUN                 => 'RUN',
    ActivitiesConstants::AT_VIRTUAL_RIDE        => 'VIRTUAL_RIDE',
    ActivitiesConstants::AT_NORDIC_SKI          => 'NORDIC_SKI',
    ActivitiesConstants::AT_WALK                => 'WALK',
    ActivitiesConstants::AT_SWIM                => 'SWIM',
    ActivitiesConstants::AT_ALPINE_SKI          => 'ALPINE_SKI',
    ActivitiesConstants::AT_BACKCOUNTRY_SKI     => 'BACKCOUNTRY_SKI',
    ActivitiesConstants::AT_CANOEING            => 'CANOEING',
    ActivitiesConstants::AT_CROSSFIT            => 'CROSSFIT',
    ActivitiesConstants::AT_E_BIKE_RIDE         => 'E_BIKE_RIDE',
    ActivitiesConstants::AT_ELLIPTICAL          => 'ELLIPTICAL',
    ActivitiesConstants::AT_HIKE                => 'HIKE',
    ActivitiesConstants::AT_ICE_SKATE           => 'ICE_SKATE',
    ActivitiesConstants::AT_INLINE_SKATE        => 'INLINE_SKATE',
    ActivitiesConstants::AT_KAYAKING            => 'KAYAKING',
    ActivitiesConstants::AT_KITESURF            => 'KITESURF',
    ActivitiesConstants::AT_ROCK_CLIMBING       => 'ROCK_CLIMBING',
    ActivitiesConstants::AT_ROLLER_SKI          => 'ROLLER_SKI',
    ActivitiesConstants::AT_ROWING              => 'ROWING',
    ActivitiesConstants::AT_SNOWBOARD           => 'SNOWBOARD',
    ActivitiesConstants::AT_SNOWSHOE            => 'SNOWSHOE',
    ActivitiesConstants::AT_STAIR_STEPPER       => 'STAIR_STEPPER',
    ActivitiesConstants::AT_STAND_UP_PADDLING   => 'STAND_UP_PADDLING',
    ActivitiesConstants::AT_SURFING             => 'SURFING',
    ActivitiesConstants::AT_VIRTUAL_RUN         => 'VIRTUAL_RUN',
    ActivitiesConstants::AT_WEIGHT_TRAINING     => 'WEIGHT_TRAINING',
    ActivitiesConstants::AT_WINDSURF            => 'WINDSURF',
    ActivitiesConstants::AT_WORKOUT             => 'WORKOUT',
    ActivitiesConstants::AT_YOGA                => 'YOGA',
    ActivitiesConstants::AT_DANCE               => 'DANCE',
    ActivitiesConstants::AT_SKIALP              => 'SKIALP',
    ActivitiesConstants::AT_OTHER               => 'OTHER',
  );

  public static $ACTIVITIES_ORDER               = array(
    ActivitiesConstants::AT_RIDE,
    ActivitiesConstants::AT_RUN,
    ActivitiesConstants::AT_NORDIC_SKI,
    ActivitiesConstants::AT_WALK,
    ActivitiesConstants::AT_SWIM,
    ActivitiesConstants::AT_ALPINE_SKI,
    ActivitiesConstants::AT_BACKCOUNTRY_SKI,
    ActivitiesConstants::AT_VIRTUAL_RIDE,
    ActivitiesConstants::AT_CANOEING,
    ActivitiesConstants::AT_CROSSFIT,
    ActivitiesConstants::AT_E_BIKE_RIDE,
    ActivitiesConstants::AT_ELLIPTICAL,
    ActivitiesConstants::AT_HIKE,
    ActivitiesConstants::AT_ICE_SKATE,
    ActivitiesConstants::AT_INLINE_SKATE,
    ActivitiesConstants::AT_KAYAKING,
    ActivitiesConstants::AT_KITESURF,
    ActivitiesConstants::AT_ROCK_CLIMBING,
    ActivitiesConstants::AT_ROLLER_SKI,
    ActivitiesConstants::AT_ROWING,
    ActivitiesConstants::AT_SNOWBOARD,
    ActivitiesConstants::AT_SNOWSHOE,
    ActivitiesConstants::AT_STAIR_STEPPER,
    ActivitiesConstants::AT_STAND_UP_PADDLING,
    ActivitiesConstants::AT_SURFING,
    ActivitiesConstants::AT_VIRTUAL_RUN,
    ActivitiesConstants::AT_WEIGHT_TRAINING,
    ActivitiesConstants::AT_WINDSURF,
    ActivitiesConstants::AT_WORKOUT,
    ActivitiesConstants::AT_YOGA,
    ActivitiesConstants::AT_DANCE,
    ActivitiesConstants::AT_SKIALP,
    ActivitiesConstants::AT_OTHER,
  );

  public static $ACTIVITY_TYPE_BY_FILE_STRING   = array(
    'Ride'                                      => ActivitiesConstants::AT_RIDE,
    'Run'                                       => ActivitiesConstants::AT_RUN,
    'VirtualRide'                               => ActivitiesConstants::AT_VIRTUAL_RIDE,
    'NordicSki'                                 => ActivitiesConstants::AT_NORDIC_SKI,
    'Walk'                                      => ActivitiesConstants::AT_WALK,
    'Swim'                                      => ActivitiesConstants::AT_SWIM,
    'AlpineSki'                                 => ActivitiesConstants::AT_ALPINE_SKI,
    'BackcountrySki'                            => ActivitiesConstants::AT_BACKCOUNTRY_SKI,
    'Canoeing'                                  => ActivitiesConstants::AT_CANOEING,
    'Crossfit'                                  => ActivitiesConstants::AT_CROSSFIT,
    'EBikeRide'                                 => ActivitiesConstants::AT_E_BIKE_RIDE,
    'Elliptical'                                => ActivitiesConstants::AT_ELLIPTICAL,
    'Hike'                                      => ActivitiesConstants::AT_HIKE,
    'IceSkate'                                  => ActivitiesConstants::AT_ICE_SKATE,
    'InlineSkate'                               => ActivitiesConstants::AT_INLINE_SKATE,
    'Kayaking'                                  => ActivitiesConstants::AT_KAYAKING,
    'Kitesurf'                                  => ActivitiesConstants::AT_KITESURF,
    'RockClimbing'                              => ActivitiesConstants::AT_ROCK_CLIMBING,
    'RollerSki'                                 => ActivitiesConstants::AT_ROLLER_SKI,
    'Rowing'                                    => ActivitiesConstants::AT_ROWING,
    'Snowboard'                                 => ActivitiesConstants::AT_SNOWBOARD,
    'Snowshoe'                                  => ActivitiesConstants::AT_SNOWSHOE,
    'StairStepper'                              => ActivitiesConstants::AT_STAIR_STEPPER,
    'StandUpPaddling'                           => ActivitiesConstants::AT_STAND_UP_PADDLING,
    'Surfing'                                   => ActivitiesConstants::AT_SURFING,
    'VirtualRun'                                => ActivitiesConstants::AT_VIRTUAL_RUN,
    'WeightTraining'                            => ActivitiesConstants::AT_WEIGHT_TRAINING,
    'Windsurf'                                  => ActivitiesConstants::AT_WINDSURF,
    'Workout'                                   => ActivitiesConstants::AT_WORKOUT,
    'Yoga'                                      => ActivitiesConstants::AT_YOGA,
    'Dance'                                     => ActivitiesConstants::AT_DANCE,
    'Skialp'                                    => ActivitiesConstants::AT_SKIALP,
    'Other'                                     => ActivitiesConstants::AT_OTHER,
  );

  public static $ACTIVITIES_WITH_AVERAGE_SPEED  = array(
    ActivitiesConstants::AT_RIDE,
    ActivitiesConstants::AT_VIRTUAL_RIDE,
    ActivitiesConstants::AT_NORDIC_SKI,
    ActivitiesConstants::AT_ALPINE_SKI,
    ActivitiesConstants::AT_BACKCOUNTRY_SKI,
    ActivitiesConstants::AT_E_BIKE_RIDE,
    ActivitiesConstants::AT_INLINE_SKATE,
    ActivitiesConstants::AT_ROLLER_SKI,
    ActivitiesConstants::AT_SNOWBOARD,
  );

  public static $ACTIVITIES_WITH_AVERAGE_PACE   = array(
    ActivitiesConstants::AT_RUN,
    ActivitiesConstants::AT_NORDIC_SKI,
    ActivitiesConstants::AT_WALK,
    ActivitiesConstants::AT_SWIM,
    ActivitiesConstants::AT_BACKCOUNTRY_SKI,
    ActivitiesConstants::AT_VIRTUAL_RUN,
  );
  // }}}

  // GPX file processors {{{
  const GFP_PHP                                 = 1;
  const GFP_STRAVA                              = 2;

  public static $SUPPORTED_GPX_FILE_PROCESSORS  = array(
    ActivitiesConstants::GFP_STRAVA,
  );

  public static $GPX_FILE_PROCESSORS_NAMES      = array(
    ActivitiesConstants::GFP_PHP                => 'PHP',
    ActivitiesConstants::GFP_STRAVA             => 'Strava',
  );
  // }}}

}

?>
