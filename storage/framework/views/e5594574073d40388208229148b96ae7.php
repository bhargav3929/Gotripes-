<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Notification - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: 2px solid #2196f3;
        }
        .header {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #0d47a1 100%);
            color: #ffffff;
            padding: 35px 30px;
            text-align: center;
        }
        .header h1 { margin: 0; font-size: 24px; font-weight: bold; }
        .header p { margin: 10px 0 0; font-size: 14px; opacity: 0.9; }
        .admin-badge {
            display: inline-block;
            padding: 6px 16px;
            background: linear-gradient(135deg, #FFD23F 0%, #ffc107 100%);
            color: #1a1a1a;
            border-radius: 25px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 12px;
        }
        .status-bar {
            padding: 15px 30px;
            text-align: center;
            font-weight: bold;
            font-size: 15px;
        }
        .status-bar.success { background: #d4edda; color: #155724; border-bottom: 3px solid #28a745; }
        .status-bar.failed { background: #f8d7da; color: #721c24; border-bottom: 3px solid #dc3545; }
        .status-bar.cancelled { background: #fff3cd; color: #856404; border-bottom: 3px solid #ffc107; }
        .content { padding: 30px; background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%); }
        .info-box {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 18px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .info-box.highlight {
            border-color: #2196f3;
            border-width: 2px;
        }
        .section-header {
            margin: -20px -20px 16px -20px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-row:last-child { border-bottom: none; }
        .label { font-weight: 600; color: #495057; flex: 1; font-size: 13px; }
        .value { flex: 2; text-align: right; color: #212529; font-weight: 500; font-size: 13px; }

        /* Invoice-style table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .invoice-table th {
            background: #0d47a1;
            color: #fff;
            padding: 10px 15px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
        }
        .invoice-table tr:last-child td { border-bottom: none; }
        .invoice-table .total-row td {
            font-weight: bold;
            font-size: 15px;
            background: #e3f2fd;
            border-top: 2px solid #0d47a1;
        }

        .quick-actions {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            border-radius: 10px;
            padding: 18px;
            margin: 20px 0;
        }
        .quick-actions strong { color: #0d47a1; }
        .quick-actions ul { margin: 10px 0; padding-left: 22px; line-height: 1.8; font-size: 13px; }
        .footer {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #0d47a1 100%);
            color: #ffffff;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }
        .icon { font-size: 14px; margin-right: 5px; }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .header { padding: 25px 20px; }
            .content { padding: 20px; }
            .info-box { padding: 16px; }
            .section-header { margin: -16px -16px 14px -16px; padding: 8px 16px; font-size: 12px; }
            .detail-row { flex-direction: column; padding: 6px 0; }
            .value { text-align: left; margin-top: 3px; }
            .invoice-table th, .invoice-table td { padding: 8px 10px; font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span class="icon">📊</span>Payment Notification</h1>
            <p><?php echo e(ucfirst($bookingType)); ?> booking payment update</p>
            <div><span class="admin-badge">Admin Dashboard</span></div>
        </div>

        <div class="status-bar <?php echo e($isSuccess ? 'success' : ($isFailed ? 'failed' : 'cancelled')); ?>">
            <?php if($isSuccess): ?>
                ✅ PAYMENT SUCCESSFUL — <?php echo e($data['order_id'] ?? 'N/A'); ?>

            <?php elseif($isFailed): ?>
                ❌ PAYMENT FAILED — <?php echo e($data['order_id'] ?? 'N/A'); ?>

            <?php else: ?>
                ⚠️ PAYMENT CANCELLED — <?php echo e($data['order_id'] ?? 'N/A'); ?>

            <?php endif; ?>
        </div>

        <div class="content">
            <!-- Customer Details -->
            <div class="info-box highlight">
                <h3 class="section-header"><span class="icon">👤</span>Customer Details</h3>

                <div class="detail-row">
                    <span class="label">Full Name:</span>
                    <span class="value"><?php echo e($data['name'] ?? ($data['UAEV_first_name'] ?? '') . ' ' . ($data['UAEV_last_name'] ?? '')); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo e($data['email'] ?? ($data['UAEV_email'] ?? 'N/A')); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Phone:</span>
                    <span class="value"><?php echo e($data['phone'] ?? ($data['UAEV_phone'] ?? 'N/A')); ?></span>
                </div>
                <?php if(!empty($data['address'])): ?>
                <div class="detail-row">
                    <span class="label">Address:</span>
                    <span class="value"><?php echo e($data['address']); ?></span>
                </div>
                <?php endif; ?>
                <?php if(!empty($data['nationality'])): ?>
                <div class="detail-row">
                    <span class="label">Nationality:</span>
                    <span class="value"><?php echo e($data['nationality']); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Activity / Booking Details -->
            <div class="info-box">
                <h3 class="section-header"><span class="icon">🎫</span><?php echo e(ucfirst($bookingType)); ?> Details</h3>

                <?php if($bookingType === 'activity'): ?>
                    <div class="detail-row">
                        <span class="label">Activity:</span>
                        <span class="value"><strong><?php echo e($data['activity_name'] ?? ($data['activityName'] ?? 'N/A')); ?></strong></span>
                    </div>
                    <?php if(!empty($data['activityLocation'])): ?>
                    <div class="detail-row">
                        <span class="label">Location:</span>
                        <span class="value"><?php echo e($data['activityLocation']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="detail-row">
                        <span class="label">Date:</span>
                        <span class="value"><?php echo e($data['booking_date'] ?? ($data['date'] ?? 'N/A')); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Adults:</span>
                        <span class="value"><?php echo e($data['adults'] ?? '0'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Children:</span>
                        <span class="value"><?php echo e($data['childrens'] ?? '0'); ?></span>
                    </div>
                    <?php if(!empty($data['transfer'])): ?>
                    <div class="detail-row">
                        <span class="label">Transfer:</span>
                        <span class="value"><?php echo e(ucwords(str_replace('_', ' ', $data['transfer']))); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($data['remarks'])): ?>
                    <div class="detail-row">
                        <span class="label">Remarks:</span>
                        <span class="value"><?php echo e($data['remarks']); ?></span>
                    </div>
                    <?php endif; ?>
                <?php elseif($bookingType === 'visa'): ?>
                    <div class="detail-row">
                        <span class="label">Visa Duration:</span>
                        <span class="value"><?php echo e($data['UAEV_visaDuration'] ?? 'N/A'); ?> Days</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Created:</span>
                        <span class="value"><?php echo e($data['UAEV_created_date'] ?? 'N/A'); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Payment / Invoice Details -->
            <div class="info-box highlight">
                <h3 class="section-header"><span class="icon">💰</span>Payment Confirmation / Invoice</h3>

                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="text-align: right;">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Order ID</td>
                            <td style="text-align: right;"><strong><?php echo e($data['order_id'] ?? 'N/A'); ?></strong></td>
                        </tr>
                        <tr>
                            <td>Payment Status</td>
                            <td style="text-align: right;">
                                <strong style="color: <?php echo e($isSuccess ? '#28a745' : ($isFailed ? '#dc3545' : '#ff9800')); ?>;">
                                    <?php echo e($paymentStatus); ?>

                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Payment Method</td>
                            <td style="text-align: right;"><?php echo e($data['payment_mode'] ?? 'Nomod Checkout'); ?></td>
                        </tr>
                        <?php if(!empty($data['tracking_id'])): ?>
                        <tr>
                            <td>Tracking ID</td>
                            <td style="text-align: right;"><?php echo e($data['tracking_id']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if(!empty($data['bank_ref_no'])): ?>
                        <tr>
                            <td>Bank Reference</td>
                            <td style="text-align: right;"><?php echo e($data['bank_ref_no']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Processed At</td>
                            <td style="text-align: right;"><?php echo e($data['sent_at'] ?? now()->format('M j, Y g:i A')); ?></td>
                        </tr>
                        <?php if($isFailed && !empty($data['failure_message'])): ?>
                        <tr>
                            <td style="color: #dc3545;">Failure Reason</td>
                            <td style="text-align: right; color: #dc3545;"><?php echo e($data['failure_message']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr class="total-row">
                            <td>Total Amount</td>
                            <td style="text-align: right;"><?php echo e($data['amount'] ?? '0'); ?> <?php echo e($data['currency'] ?? 'AED'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Supplier Info (for activity bookings) -->
            <?php if(!empty($data['supplierName']) || !empty($data['supplier_name'])): ?>
            <div class="info-box">
                <h3 class="section-header"><span class="icon">🏢</span>Supplier Info</h3>
                <div class="detail-row">
                    <span class="label">Name:</span>
                    <span class="value"><?php echo e($data['supplierName'] ?? ($data['supplier_name'] ?? 'N/A')); ?></span>
                </div>
                <?php if(!empty($data['supplierEmail']) || !empty($data['supplier_email'])): ?>
                <div class="detail-row">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo e($data['supplierEmail'] ?? ($data['supplier_email'] ?? '')); ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="label">Supplier Notified:</span>
                    <span class="value" style="color: #28a745;">Yes — Email sent automatically</span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <strong><span class="icon">📋</span>Admin Actions Required:</strong>
                <ul>
                    <?php if($isSuccess): ?>
                        <li>Verify payment in Nomod dashboard</li>
                        <li>Confirm booking with supplier if not auto-notified</li>
                        <li>Update internal records / CRM</li>
                        <li>Send activity voucher / tickets to customer if applicable</li>
                    <?php elseif($isFailed): ?>
                        <li>Check Nomod dashboard for failure details</li>
                        <li>Contact customer to retry payment or offer alternatives</li>
                        <li>Booking reserved for 24 hours — follow up promptly</li>
                    <?php else: ?>
                        <li>Customer cancelled the payment</li>
                        <li>Follow up with customer if booking was important</li>
                        <li>No charges were made</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p><strong><?php echo e(config('app.name', 'Go Trips')); ?> — Admin Notification</strong></p>
            <p>Generated: <?php echo e(now()->format('M j, Y g:i A')); ?> (GST)</p>
            <p style="font-size: 11px; opacity: 0.7; margin-top: 8px;">
                This is an automated admin notification. Do not forward this email — it contains sensitive payment data.
            </p>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/bhargav/Desktop/Gotrips/Gotripes-/resources/views/emails/admin-payment-notification.blade.php ENDPATH**/ ?>