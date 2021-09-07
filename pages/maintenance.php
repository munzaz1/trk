<div id="main">
  <div id="header">
    <div class="headerContent">
      <div class="header_l headerLogo">
        <a href="./"><img src="../images/logoWhite.png" title="<?=AboutConstants::APPLICATION_NAME?>" /></a>
      </div>
      <div class="header_l">
        <h3><?=AboutConstants::APPLICATION_NAME?></h3>
      </div>
      <div class="clear">&nbsp;</div>
    </div>
  </div>

  <div class="container">

    <div class="pageContent">

      <p>
        <h1><?=t::m('PAGE_MAINTENANCE_TITLE')?></h1>
      </p>

      <?php
      if ($tp['ERROR_MESSAGE'] != '') {
        ?>
        <p id="generalError">
          <?=$tp['ERROR_MESSAGE']?>
        </p>
        <?php
      }
      ?>

      <?php
      if ($tp['NOTIFICATION_MESSAGE'] != '') {
        ?>
        <p class="notificationMessageInfo">
          <?=$tp['NOTIFICATION_MESSAGE']?>
        </p>
        <?php
      }
      ?>

      <?php
      if ($tp['MESSAGE'] != '') {
        ?>
        <p class="maintenanceMessage">
          <?=$tp['MESSAGE']?>
        </p>
        <?php
      }
      ?>

    </div>
  </div>
</div>
