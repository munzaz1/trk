<?

class PhotosMethods {

  public static function determinePhotosDictionary($key, $userId) {
    $photosDictionary = 'photos/' . $userId;
    if (!file_exists($photosDictionary)) {
      mkdir($photosDictionary, 0777, true);
    }
    $photosDictionary = $photosDictionary . '/' . $key;
    if (!file_exists($photosDictionary)) {
      mkdir($photosDictionary, 0777, true);
    }
    return $photosDictionary;
  }


  public static function determineActivityPhotoFileName($photosDictionary, $activityRow) {
    $fileName = NULL;
    while (($fileName === NULL) || (file_exists($fileName))) {
      $baseFileName = date('Y-m-d', $activityRow['startTime']) . '_' . $activityRow['id'] . '_' . bin2hex(random_bytes(4));
      $fileName = $photosDictionary . '/' . $baseFileName . '.jpg';
    }
    return $fileName;
  }


  public static function determineGearPhotoFileName($photosDictionary, $gearRow) {
    $fileName = NULL;
    while (($fileName === NULL) || (file_exists($fileName))) {
      $baseFileName = $gearRow['id'] . '_' . bin2hex(random_bytes(4));
      $fileName = $photosDictionary . '/' . $baseFileName . '.jpg';
    }
    return $fileName;
  }


  public static function downloadPhotoAndCreateThumbnail($photoURL, $fileName) {
    file_put_contents($fileName, file_get_contents($photoURL));
    PhotosMethods::createThumbnail($fileName);
  }


  public static function getThumbnailFileName($photoFileName) {
    $pathParts = pathinfo($photoFileName);
    $thumbnailFileName = $pathParts['dirname'] . '/' . Settings::$S['PHOTO_THUMBNAIL_PREFIX'] . $pathParts['basename'];
    return $thumbnailFileName;
  }


  public static function removePhotoWithThumbnail($fileName) {
    $thumbnailFileName = PhotosMethods::getThumbnailFileName($fileName);
    unlink($fileName);
    unlink($thumbnailFileName);
  }


  public static function createThumbnail($fileName) {
    $thumbnailFileName = PhotosMethods::getThumbnailFileName($fileName);

    $sourceImg = @imagecreatefromjpeg($fileName);

    $newWeight = Settings::$S['PHOTO_THUMBNAIL_WIDTH'];
    $newHeight = Settings::$S['PHOTO_THUMBNAIL_HEIGHT'];
    $newWeightHeightRatio = $newWeight / $newHeight;

    $origWeight = imagesx($sourceImg);
    $origHeight = imagesy($sourceImg);

    $weightRatio = ($newWeight / $origWeight);
    $heightRatio = ($newHeight / $origHeight);

    if ($origWeight >= $origHeight ) {  // landscape (square)
      $cropHeight = $origHeight;
      $cropWeight = round($cropHeight * $newWeightHeightRatio);
    } else {                            // portrait
      $cropWeight = $origWeight;
      $cropHeight = round($cropWeight / $newWeightHeightRatio);
    }
    $destImg = imagecreatetruecolor($newWeight, $newHeight);

    $sourceOffsetX = round(($origWeight - $cropWeight) / 2);
    $sourceOffsetY = round(($origHeight - $cropHeight) / 2);

    imagecopyresampled($destImg, $sourceImg, 0, 0, $sourceOffsetX, $sourceOffsetY, $newWeight, $newHeight, $cropWeight, $cropHeight);

    imagejpeg($destImg, $thumbnailFileName);
    imagedestroy($destImg);
    imagedestroy($sourceImg);
  }


  public static function downloadAndRegisterStravaPhotos($stravaPhotos, $pageRequest, $activityRow) {
    $photosDictionary = PhotosMethods::determinePhotosDictionary(date('Y', $activityRow['startTime']), $pageRequest->sessionData->userId);
    foreach ($stravaPhotos as $stravaPhoto) {
      $fileName = PhotosMethods::determineActivityPhotoFileName($photosDictionary, $activityRow);
      PhotosMethods::downloadPhotoAndCreateThumbnail($stravaPhoto['url'], $fileName);
      DBMethods::registerPhoto_Activity(
        $pageRequest->db,                       // $db
        $activityRow['id'],                     // $activityId
        $fileName,                              // $fileName
        '',                                     // $description
        NULL,                                   // $rating
        $stravaPhoto['isDefault'],              // $isDefault
        $stravaPhoto['latitude'],               // $latitude
        $stravaPhoto['longitude'],              // $longitude
        $stravaPhoto['takenTime']               // $takenTime
      );
    }
  }

}

?>
