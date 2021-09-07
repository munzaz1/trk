<?php

class Settings {

  public static $S                            = array(
    'LANGUAGE'                                => 'en',
    'TIME_ZONE'                               => 'Europe/Prague',
    'ENCODING'                                => 'utf-8',

    'ABSOLUTE_URL'                            => 'http://localhost/_xxx_/',

    'ADMIN_EMAIL'                             => '_xxx_@example.com',

    'AUTO_LOGIN_USER_ID'                      => NULL,          // NULL or ID of the user (should be the same as DEFAULT_USER_ID)
    'DEFAULT_USER_ID'                         => 1,
    'PASSWORD'                                => '_xxx_',

    'DB_SERVER'                               => '_xxx_',
    'DB_USER'                                 => '_xxx_',
    'DB_PASSWORD'                             => '_xxx_',
    'DB_DATABASE'                             => '_xxx_',
    'DB_TABLE_PREFIX'                         => '',

    'METRIC_UNITS'                            => True,
    'DATE_FORMAT'                             => 'j.n.Y',
    'DATE_FORMAT_WITHOUT_YEAR'                => 'j.n.',
    'DATE_TIME_FORMAT'                        => 'j.n.Y H:i',
    'DATE_TIME_FORMAT_SECONDS'                => 'j.n.Y H:i:s',
    'DATE_TIME_FORMAT_WITHOUT_YEAR'           => 'j.n. H:i',
    'DECIMAL_DELIMITER'                       => ',',
    'THOUSANDS_DELIMITER'                     => ' ',

    'GPX_DIR'                                 => 'gpx',
    'LIMIT_FOR_DETMINING_LOADED_GPX_FILES'    => 1000,

    'PHOTO_THUMBNAIL_PREFIX'                  => 'tmb_',
    'PHOTO_THUMBNAIL_WIDTH'                   => 200,
    'PHOTO_THUMBNAIL_HEIGHT'                  => 200,
    'PHOTO_RATING_MIN'                        => 1,
    'PHOTO_RATING_MAX'                        => 5,
    'PHOTO_RATING_DEFAULT'                    => 3,

    'LEAFLET_MAP_TILES'                       => 'https://mapserver.mapy.cz/turist-m/{z}-{x}-{y}',
                                               //http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png

    'MAX_EXTERNAL_ACTIVITIES_TO_FETCH'        => 100,

    'ACTIVITY_SELECTION_TIME_TOLERANCE'       => 10,       // +- 10 seconds

    'MIN_YEAR'                                => 1800,

    'EMPTY_VALUE_FOR_SELECT_BOX'              => '__EMPTY__',

    // location {{{
    'USE_LOCATION_SERVICE_TO_FILL_DATA'       => True,
    'LOCATION_SERVICE_LANGUAGE'               => 'cz',
    'LOCATION_SERVICE_URL'                    => 'https://nominatim.openstreetmap.org/reverse.php?format=json&lat=%s&lon=%s&accept-language=%s',
    // }}}

    // weather {{{
    'GET_WEATHER_DATA_FROM_DESCRIPTION'       => True,
    // }}}
  );

}

?>
