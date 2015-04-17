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

    private $error = array();
    
    public function index() {
	// Load the language
	$this->load->language('payment/bitdrive_standard');
        
	// Set the page title
	$this->document->setTitle($this->language->get('heading_title'));
        
	// Load the required models
        $this->load->model('setting/setting');
	$this->load->model('localisation/order_status');

	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->_validate()) {
	    $this->model_setting_setting->editSetting('bitdrive_standard', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }
        
        $this->_buildBreadcrumbs();
        
        $this->_setDataAttributes();
        
        $this->template = 'payment/bitdrive_standard.tpl';
        $this->children = array(
                'common/header',
                'common/footer'
        );
        $this->response->setOutput($this->render());
    }
    
    private function _setDataAttributes() {
        // Text resources
	$this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
	$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
	
	// Form labels
	$this->data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
	$this->data['entry_ipn_secret'] = $this->language->get('entry_ipn_secret');
	$this->data['entry_debug'] = $this->language->get('entry_debug');
	$this->data['entry_created_status'] = $this->language->get('entry_created_status');
	$this->data['entry_completed_status'] = $this->language->get('entry_completed_status');
	$this->data['entry_cancelled_status'] = $this->language->get('entry_cancelled_status');
	$this->data['entry_expired_status'] = $this->language->get('entry_expired_status');
	$this->data['entry_status'] = $this->language->get('entry_status');
	$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
	
	// Buttons
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');
        
	// Form actions
        $this->data['action'] = $this->url->link('payment/bitdrive_standard', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
        
	// Form fields
        $this->data['bitdrive_standard_merchant_id'] = isset($this->request->post['bitdrive_standard_merchant_id'])
            ? $this->request->post['bitdrive_standard_merchant_id'] : $this->config->get('bitdrive_standard_merchant_id');
       
        $this->data['bitdrive_standard_ipn_secret'] = isset($this->request->post['bitdrive_standard_ipn_secret'])
            ? $this->request->post['bitdrive_standard_ipn_secret'] : $this->config->get('bitdrive_standard_ipn_secret');
	
	$this->data['bitdrive_standard_debug'] = isset($this->request->post['bitdrive_standard_debug'])
            ? $this->request->post['bitdrive_standard_debug'] : $this->config->get('bitdrive_standard_debug');
	
	$this->data['bitdrive_standard_created_status_id'] = isset($this->request->post['bitdrive_standard_created_status_id'])
            ? $this->request->post['bitdrive_standard_created_status_id']
	    : $this->config->get('bitdrive_standard_created_status_id');
	
	$this->data['bitdrive_standard_completed_status_id'] = isset($this->request->post['bitdrive_standard_completed_status_id'])
            ? $this->request->post['bitdrive_standard_completed_status_id']
	    : $this->config->get('bitdrive_standard_completed_status_id');
	
	$this->data['bitdrive_standard_cancelled_status_id'] = isset($this->request->post['bitdrive_standard_cancelled_status_id'])
            ? $this->request->post['bitdrive_standard_cancelled_status_id']
	    : $this->config->get('bitdrive_standard_cancelled_status_id');
	
	$this->data['bitdrive_standard_expired_status_id'] = isset($this->request->post['bitdrive_standard_expired_status_id'])
            ? $this->request->post['bitdrive_standard_expired_status_id']
	    : $this->config->get('bitdrive_standard_expired_status_id');
	
	$this->data['bitdrive_standard_status'] = isset($this->request->post['bitdrive_standard_status'])
            ? $this->request->post['bitdrive_standard_status'] : $this->config->get('bitdrive_standard_status');
	    
	$this->data['bitdrive_standard_sort_order'] = isset($this->request->post['bitdrive_standard_sort_order'])
            ? $this->request->post['bitdrive_standard_sort_order'] : $this->config->get('bitdrive_standard_sort_order');
	
	// Error messages
	$this->data['error_warning'] = (isset($this->error['warning'])) ? $this->error['warning'] : '';
	$this->data['error_merchant_id'] = (isset($this->error['merchant_id'])) ? $this->error['merchant_id'] : '';
    }
    
    private function _buildBreadcrumbs() {
        $this->data['breadcrumbs'] = array();
        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/bitdrive_standard', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );
    }
    
    private function _validate() {
        if (!$this->user->hasPermission('modify', 'payment/bitdrive_standard')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!isset($this->request->post['bitdrive_standard_merchant_id'])
	    || strlen(trim($this->request->post['bitdrive_standard_merchant_id'])) == 0) {
            $this->error['merchant_id'] = $this->language->get('error_merchant_id');
        }

        return (empty($this->error));
    }
}

?>