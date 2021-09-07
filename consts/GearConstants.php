<?

class GearConstants {

  // gears types {{{
  const GT_BIKE                       = 1;
  const GT_RUNNING_SHOES              = 2;
  const GT_NORDIC_SKI                 = 3;
  const GT_ALPINE_SKI                 = 4;
  const GT_OTHER                      = 100;
  // }}}

  public static $GEAR_INTERNAL_NAMES  = array(
    GearConstants::GT_BIKE            => 'BIKE',
    GearConstants::GT_RUNNING_SHOES   => 'RUNNING_SHOES',
    GearConstants::GT_NORDIC_SKI      => 'NORDIC_SKI',
    GearConstants::GT_ALPINE_SKI      => 'ALPINE_SKI',
    GearConstants::GT_OTHER           => 'OTHER',
  );

}

?>
