<?

session_start();

if (!file_exists('Settings.php')) {
  echo 'Settings.php not found. See <a href="./maintenance/">maintenance</a> instructions.';
  exit;
}

require 'Settings.php';

include 'language/' . Settings::$S['LANGUAGE'] . '.php';
include 'language/en.php';

include 'utils/Translation.php';

include 'consts/AboutConstants.php';
include 'consts/ActivitiesConstants.php';
include 'consts/ClientBuildConstants.php';
include 'consts/ExternalSourcesConstants.php';
include 'consts/GearConstants.php';
include 'consts/GUIConstants.php';
include 'consts/HandlersConstants.php';
include 'consts/RegExpConstants.php';
include 'consts/SettingsConstants.php';
include 'consts/StravaConstants.php';
include 'consts/TimeConstants.php';
include 'consts/WeatherConstants.php';

include 'utils/DBMethods.php';
include 'utils/AuxiliaryMethods.php';
include 'utils/ConversionMethods.php';
include 'utils/DBConnector.php';
include 'utils/Exceptions.php';
include 'utils/Form.php';
include 'utils/GlobalSettings.php';
include 'utils/GPXMethods.php';
include 'utils/FormMethods.php';
include 'utils/HTMLBlockMethods.php';
include 'utils/Page.php';
include 'utils/PageRequest.php';
include 'utils/SessionData.php';
include 'utils/SettingsMethods.php';
include 'utils/StatsMethods.php';
include 'utils/StravaConnector.php';
include 'utils/TextFormattingMethods.php';
include 'utils/WebApplication.php';
include 'utils/ActivityStatsStorage.php';
include 'utils/PhotosMethods.php';

include 'handlers/Handler_ACTIVITIES.php';
include 'handlers/Handler_DIARY.php';
include 'handlers/Handler_GPX.php';
include 'handlers/Handler_PHOTO.php';
include 'handlers/Handler_PROFILE.php';
include 'handlers/Handler_STATS.php';
include 'handlers/Handler_USER.php';

try {
  $app = new WebApplication();

  $app->registerPageHandler('login',                              array('Handler_USER', 'loginHandler'), 0);
  $app->registerPageHandler('logout',                             array('Handler_USER', 'logoutHandler'), 0);
  $app->registerPageHandler('',                                   array('Handler_ACTIVITIES', 'activityHandler'), 1);
  $app->registerPageHandler('diary',                              array('Handler_DIARY', 'diaryHandler'), 1);
  $app->registerPageHandler('diary-ae',                           array('Handler_DIARY', 'addOrEditDiaryItemHandler'), 1);
  $app->registerPageHandler('diary-categories',                   array('Handler_DIARY', 'diaryCategoriesHandler'), 1);
  $app->registerPageHandler('diary-category-ae',                  array('Handler_DIARY', 'addOrEditDiaryCategory'), 1);
  $app->registerPageHandler('summary',                            array('Handler_ACTIVITIES', 'summaryHandler'), 1);
  $app->registerPageHandler('activity',                           array('Handler_ACTIVITIES', 'activityHandler'), 1);
  $app->registerPageHandler('activity-ae',                        array('Handler_ACTIVITIES', 'addOrEditActivityHandler'), 1);
  $app->registerPageHandler('fetch-activity-location',            array('Handler_ACTIVITIES', 'fetchActivityLocationHandler'), 1);
  $app->registerPageHandler('fetch-activity-photos-from-strava',  array('Handler_ACTIVITIES', 'fetchActivityPhotosFromStravaHandler'), 1);
  $app->registerPageHandler('synchronize-strava',                 array('Handler_ACTIVITIES', 'synchronizeStravaHandler'), 1);
  $app->registerPageHandler('synchronize-strava-auth',            array('Handler_ACTIVITIES', 'synchronizeStravaAuthHandler'), 1);
  $app->registerPageHandler('import-csv',                         array('Handler_ACTIVITIES', 'importCSVHandler'), 1);
  $app->registerPageHandler('gpx-files',                          array('Handler_GPX', 'gpxFilesHandler'), 1);
  $app->registerPageHandler('load-gpx',                           array('Handler_GPX', 'loadGPXHandler'), 1);
  $app->registerPageHandler('stats',                              array('Handler_STATS', 'statsHandler'), 1);
  $app->registerPageHandler('cumulativeStats',                    array('Handler_STATS', 'cumulativeStatsHandler'), 1);
  $app->registerPageHandler('profile',                            array('Handler_PROFILE', 'profileHandler'), 1);
  $app->registerPageHandler('weights',                            array('Handler_PROFILE', 'weightsHandler'), 1);
  $app->registerPageHandler('gear',                               array('Handler_PROFILE', 'gearHandler'), 1);
  $app->registerPageHandler('gear-ae',                            array('Handler_PROFILE', 'addOrEditGearHandler'), 1);
  $app->registerPageHandler('uploadPhoto',                        array('Handler_PHOTO', 'uploadPhotoHandler'), 1);
  $app->registerPageHandler('ratePhoto',                          array('Handler_PHOTO', 'ratePhotoHandler'), 1);
  $app->registerPageHandler('removePhoto',                        array('Handler_PHOTO', 'removePhotoHandler'), 1);


  $app->doApp();

} catch (Exception $e) {
  $errorMessage = $e->getMessage();
  $errorMessage .= '<br /><br /> ' . $e->getTraceAsString();
  $page = new SimplePage(array('pages/generalError.php'), array('ERROR_MESSAGE' => $errorMessage));
  $page->printPage();
}

?>
