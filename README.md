# bitdrive-opencart-plugin
## BitDrive payment method plugin for OpenCart

Accept bitcoin payments on your OpenCart shopping cart using BitDrive Standard Checkout. Includes support for the BitDrive Instant Payment Notification (IPN) messages.

### Minimum Requirements
* PHP 5.2+
* OpenCart 1.5+

### Quick Installation
1. Extract the archived files to the OpenCart root path.
2. Log in to **OpenCart Administration** and navigate to **Extensions > Payments**.
3. Click the **Install** link for **BitDrive Standard Checkout** and then click the **Edit** link once the installation is complete.
4. Specify your **Merchant ID**.
5. If you have IPN enabled, specify your BitDrive **IPN Secret**.
6. Optionally, specify the order statuses corresponding to the IPN notification types.
7. Set the **Status** to **Enabled**.
8. Click the **Save** button.

For documentation on BitDrive merchant services, go to https://www.bitdrive.io/help/merchant-services/6-introduction
