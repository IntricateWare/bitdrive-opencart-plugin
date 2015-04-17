<?php echo $header ?>
<div id="content">
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb): ?>
            <?php echo $breadcrumb['separator'] ?><a href="<?php echo $breadcrumb['href'] ?>"><?php echo $breadcrumb['text'] ?></a>
        <?php endforeach ?>
    </div>
    
    <?php if ($error_warning): ?>
    <div class="warning"><?php echo $error_warning ?></div>
    <?php endif ?>
    
    <div class="box">
        <div class="heading">
            <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title ?></h1>
            <div class="buttons">
                <a onclick="$('#form').submit();" class="button"><span><?php echo $button_save ?></span></a>
                <a onclick="location = '<?php echo $cancel ?>';" class="button"><span><?php echo $button_cancel ?></span></a>
            </div>
        </div>
        
        <div class="content">
            <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    
                    <tr>
                        <td><span class="required">*</span> <?php echo $entry_merchant_id ?></td>
                        <td>
                            <input type="text"
                                   name="bitdrive_standard_merchant_id"
                                   value="<?php echo $bitdrive_standard_merchant_id ?>"
                                   size="40" />
                            <?php if ($error_merchant_id): ?>
                            <span class="error"><?php echo $error_merchant_id ?></span>
                            <?php endif ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><?php echo $entry_ipn_secret ?></td>
                        <td>
                            <input type="text"
                                   name="bitdrive_standard_ipn_secret"
                                   value="<?php echo $bitdrive_standard_ipn_secret ?>" />
                        </td>
                    </tr>
                    
                    <tr>
                        <td><?php echo $entry_debug ?></td>
                        <td>
                            <select name="bitdrive_standard_debug">
                                <option value="1" <?php if ($bitdrive_standard_debug): ?>selected="selected"<?php endif ?>>
                                    <?php echo $text_enabled ?>
                                </option>
                                <option value="0" <?php if (!$bitdrive_standard_debug): ?>selected="selected"<?php endif ?>>
                                    <?php echo $text_disabled ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><?php echo $entry_created_status ?></td>
                        <td>
                            <select name="bitdrive_standard_created_status_id">
                            <?php foreach ($order_statuses as $order_status): ?>
                                <option value="<?php echo $order_status['order_status_id'] ?>"
                                        <?php if($order_status['order_status_id'] == $bitdrive_standard_created_status_id): ?>
                                        selected="selected"
                                        <?php endif ?>>
                                    <?php echo $order_status['name'] ?>
                                </option>
                            <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><?php echo $entry_completed_status ?></td>
                        <td>
                            <select name="bitdrive_standard_completed_status_id">
                            <?php foreach ($order_statuses as $order_status): ?>
                                <option value="<?php echo $order_status['order_status_id'] ?>"
                                        <?php if($order_status['order_status_id'] == $bitdrive_standard_completed_status_id): ?>
                                        selected="selected"
                                        <?php endif ?>>
                                    <?php echo $order_status['name'] ?>
                                </option>
                            <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><?php echo $entry_cancelled_status ?></td>
                        <td>
                            <select name="bitdrive_standard_cancelled_status_id">
                            <?php foreach ($order_statuses as $order_status): ?>
                                <option value="<?php echo $order_status['order_status_id'] ?>"
                                        <?php if($order_status['order_status_id'] == $bitdrive_standard_cancelled_status_id): ?>
                                        selected="selected"
                                        <?php endif ?>>
                                    <?php echo $order_status['name'] ?>
                                </option>
                            <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><?php echo $entry_expired_status ?></td>
                        <td>
                            <select name="bitdrive_standard_expired_status_id">
                            <?php foreach ($order_statuses as $order_status): ?>
                                <option value="<?php echo $order_status['order_status_id'] ?>"
                                        <?php if($order_status['order_status_id'] == $bitdrive_standard_expired_status_id): ?>
                                        selected="selected"
                                        <?php endif ?>>
                                    <?php echo $order_status['name'] ?>
                                </option>
                            <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><?php echo $entry_status ?></td>
                        <td>
                            <select name="bitdrive_standard_status">
                                <option value="1" <?php if ($bitdrive_standard_status): ?>selected="selected"<?php endif ?>>
                                    <?php echo $text_enabled ?>
                                </option>
                                <option value="0" <?php if (!$bitdrive_standard_status): ?>selected="selected"<?php endif ?>>
                                    <?php echo $text_disabled ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><?php echo $entry_sort_order ?></td>
                        <td>
                            <input type="text"
                                   name="bitdrive_standard_sort_order"
                                   value="<?php echo $bitdrive_standard_sort_order ?>"
                                   size="1" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>