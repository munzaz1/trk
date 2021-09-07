<div class="pageContent">
  <form action="./login" method="post">

    <div class="shortFormFieldRow inputLabel_required">
      <?=t::m('LABEL_PASSWORD')?>
    </div>
    <div class="shortFormFieldRow">
      <?=FormMethods::getInputCode_password(
        'fPassword',                    // $name
        array('fPassword' => ''),       // $valueByField
        $tp['ERROR_MESSAGES_BY_FIELD'], // $errorMessagesByField
        1                               // $isRequired
      )?>

    </div>
    <div class="shortFormFieldRow">
      <div class="sendButton">
        <input type="submit" value="<?=t::m('LABEL_LOGIN')?>" />
      </div>
      <div class="clear">&nbsp;</div>
    </div>

    <input type="hidden" name="fLoginSend" value="1" />

  </form>
</div>
