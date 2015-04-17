<form action="<?php echo $action; ?>" method="post" id="payment">
    <input type="hidden" name="bd-cmd" value="<?php echo $cmd ?>" />
    <input type="hidden" name="bd-merchant" value="<?php echo $merchant_id ?>" />
    <input type="hidden" name="bd-currency" value="<?php echo $currency ?>" />
    <input type="hidden" name="bd-amount" value="<?php echo $amount ?>" />
    <input type="hidden" name="bd-memo" value="<?php echo $memo ?>" />
    <input type="hidden" name="bd-invoice" value="<?php echo $invoice ?>" />
    <input type="hidden" name="bd-success-url" value="<?php echo $success_url ?>" />
    <input type="hidden" name="bd-error-url" value="<?php echo $cancel_url ?>" />
</form>
<div class="buttons">
    <div class="right">
        <a id="button-confirm" class="button" onclick="$('#payment').submit();"><span><?php echo $button_confirm; ?></span></a>
    </div>
</div>