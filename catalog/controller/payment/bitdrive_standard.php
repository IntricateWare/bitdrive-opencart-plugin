<?php

/*
 * Copyright (c) 2015 IntricateWare Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class ControllerPaymentBitDriveStandard extends Controller {
    /**
     * The log prefix to use if debug mode is enabled.
     * @type string
     */
    const LOG_PREFIX = '[BitDrive Standard] ';
    
    /**
     * The 'ORDER_CREATED' notification type string.
     * @type string
     */
    const ORDER_CREATED = 'ORDER_CREATED';
    
    /**
     * The 'PAYMENT_COMPLETED' notification type string.
     * @type string
     */
    const PAYMENT_COMPLETED = 'PAYMENT_COMPLETED';
    
    /**
     * The 'TRANSACTION_CANCELLED' notification type string.
     * @type string
     */
    const TRANSACTION_CANCELLED = 'TRANSACTION_CANCELLED';
    
    /**
     * The 'TRANSACTION_EXPIRED' notification type string.
     * @type string
     */
    const TRANSACTION_EXPIRED = 'TRANSACTION_EXPIRED';
    
    /**
     * The BitDrive checkout URL.
     * @type string
     */
    private $_checkoutUrl = 'https://www.bitdrive.io/pay';
    
    /**
     * Flag which indicates whether or not debug mode is enabled.
     * @type boolean
     */
    private $_debug = false;
    
    /**
     * The required IPN message parameters.
     * 
     * @type array
     */
    private $_requiredIpnParams = array(
        'notification_type',
        'sale_id',
        'merchant_invoice',
        'amount',
        'bitcoin_amount'
    );
    
    /**
     * A list of supported currencies.
     * @type array
     */
    private $_supportedCurrencies = array(
        'BTC',
        'USD'
    );
    
    /**
     * The default controller action.
     */
    protected function index() {
        $this->language->load('payment/bitdrive_standard');
        
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['action'] = $this->_checkoutUrl;
      
        // Load models
        $this->load->model('checkout/order');
        
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        if ($order_info) {
            // Force USD if the specified currency is not supported
            $currency = in_array($order_info['currency_code'], $this->_supportedCurrencies)
                ? $order_info['currency_code'] : 'USD';
	    
            $this->data['cmd'] = 'pay';
            $this->data['merchant_id'] = $this->config->get('bitdrive_standard_merchant_id');
            $this->data['currency'] = $currency;
            $this->data['amount'] = $this->cart->getTotal();
            $this->data['memo'] = $this->_buildTransactionMemo();
            $this->data['invoice'] = $this->session->data['order_id'];
            $this->data['success_url'] = $this->url->link('checkout/success');
            $this->data['cancel_url'] = $this->url->link('checkout/checkout', '', 'SSL');
            
            $nonDefaultTemplate = $this->config->get('config_template') . '/template/payment/bitdrive_standard.tpl';
            $this->template = (file_exists(DIR_TEMPLATE . $nonDefaultTemplate))
                ? $nonDefaultTemplate : 'default/template/payment/bitdrive_standard.tpl';
                
            $this->render();
        }
    }
    
    /**
     * BitDrive IPN message handler.
     */
    public function ipn() {        
        // Only handle POST requests
        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            return;
        }
        
        // Load configuration
        $this->_debug = $this->config->get('bitdrive_standard_debug');
        $merchantId = $this->config->get('bitdrive_standard_merchant_id');
        $ipnSecret = $this->config->get('bitdrive_standard_ipn_secret');
        
        // Check for SHA 256 support
        if (!in_array('sha256', hash_algos())) {
            $this->_ipnLogEntry('The PHP installation does not support the SHA 256 hash algorithm.');
            return;
        }
        
        // Check the IPN data
        $data = file_get_contents('php://input');
        $this->_ipnLogEntry(sprintf('IPN data received: %s', $data));
        
        $json = json_decode($data);
        if (!$json) {
            $this->_ipnLogEntry('The BitDrive IPN JSON data is invalid.');
            return;
        }
        
        // Check for the IPN parameters that we need
        foreach ($this->_requiredIpnParams as $param) {
            if (!isset($json->$param) || strlen(trim($json->$param)) == 0) {
                $this->_ipnLogEntry(sprintf('Missing %s IPN parameter.', $param));
                return;
            }
        }
        
        // Verify the SHA 256 hash
        $hashString = strtoupper(hash('sha256', $json->sale_id . $merchantId . $json->merchant_invoice . $ipnSecret));
        if ($hashString != $json->hash) {
            $this->_ipnLogEntry('The notification message cannot be processed due to a hash mismatch.');
            return;
        }
        
        // Load models
        $this->load->model('checkout/order');
        $order_id = $json->merchant_invoice;
        $order_info = $this->model_checkout_order->getOrder($order_id);
        if (!$order_info) {
            $this->_ipnLogEntry(sprintf('The order was not found for order ID %s', $order_id));
            return;
        }
        
        // Check the notification type and set the corresponding order status
        $order_status_id = $this->config->get('config_order_status_id');
        switch ($json->notification_type) {
            // Order created
            case self::ORDER_CREATED:
                $order_status_id = $this->config->get('bitdrive_standard_created_status_id');
                break;
            
            // Payment completed
            case self::PAYMENT_COMPLETED:
                $order_status_id = $this->config->get('bitdrive_standard_completed_status_id');
                break;
            
            // Transaction cancelled
            case self::TRANSACTION_CANCELLED:
                $order_status_id = $this->config->get('bitdrive_standard_cancelled_status_id');
                break;
            
            // Transaction expired
            case self::TRANSACTION_EXPIRED:
                $order_status_id = $this->config->get('bitdrive_standard_expired_status_id');
                break;
        }
        
        if (!$order_info['order_status_id']) {
            $this->model_checkout_order->confirm($order_id, $order_status_id);
        } else {
            $this->model_checkout_order->update($order_id, $order_status_id);
        }
        $this->_ipnLogEntry(sprintf('Order ID %s status updated for IPN message %s', $order_id, $json->notification_type));
    }
    
    /**
     * Write IPN-related log entry.
     *
     * @param string $data
     */
    private function _ipnLogEntry($data) {
        if ($this->_debug) {
            $this->log->write(sprintf('%s %s', self::LOG_PREFIX, $data));
        }
    }
    
    /**
     * Build the transaction memo based on the Order ID and the cart contents.
     */
    private function _buildTransactionMemo() {
        $memo = sprintf('Payment for Order #%s', $this->session->data['order_id']);
        
        $items = $this->cart->getProducts();
        if (count($items) == 1) {
            $item = reset($items);
            $qty = intval($item['quantity']);
            $itemString = (($qty > 0) ? $qty . ' x ' : '') . $item['name'];
            
            $newMemo = $memo . ': ' . $itemString;
            if (strlen($newMemo) <= 200) {
                $memo = $newMemo;
            }
        }
        
        return $memo;
    }
}

 
?>