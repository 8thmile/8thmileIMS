<?php
session_start();
include('config.php');

date_default_timezone_set('Asia/Manila'); 

if(!isset($_GET['transaction_id']) || empty($_GET['transaction_id'])) {
    die("<div style='padding:20px; font-family:sans-serif; color:red;'><strong>Error:</strong> No Transaction ID provided.</div>");
}

$trans_id = mysqli_real_escape_string($conn, $_GET['transaction_id']);

$query = "SELECT s.*, p.name AS product_name, p.sku, c.client_name 
          FROM stock_out s 
          LEFT JOIN products p ON s.product_id = p.productID 
          LEFT JOIN clients c ON s.ClientID = c.id 
          WHERE s.transaction_id = '$trans_id'";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("<div style='padding:20px; font-family:sans-serif;'><strong>Error:</strong> Record not found.</div>");
}

$first_row = mysqli_fetch_assoc($result);
mysqli_data_seek($result, 0); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accountability Slip</title>
    <style>
        @page { margin: 0; size: auto; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 40px; color: #000; background-color: #fff; }
        .slip-container { max-width: 850px; margin: auto; }
        
        .header-wrapper { display: flex; align-items: center; justify-content: center; margin-bottom: 10px; }
        .logo-container { margin-right: 20px; }
        .logo-container img { max-width: 100px; height: auto; display: block; }
        .company-info { text-align: center; }
        .company-name { font-size: 22px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .company-address { font-size: 13px; margin-top: 3px; }

        .title-box { text-align: center; font-weight: bold; font-size: 18px; text-decoration: underline; text-transform: uppercase; margin-top: 5px; display: block; }

        .info-section { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; line-height: 1.4; }
        .info-column { width: 48%; }
        .info-column.right { text-align: right; }
        .label { font-weight: bold; text-transform: uppercase; }

        /* Table maintained at same size with larger font */
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table, th, td { border: 1.5px solid black; }
        
        th { 
            padding: 3px 5px; 
            font-size: 18px; /* Slightly bigger headers */
            background: #f2f2f2; 
            text-transform: uppercase; 
        }
        
        td { 
            padding: 2px 5px; /* Minimal padding to allow larger font without growing table */
            font-size: 18px; /* Increased font size as requested */
            vertical-align: middle; 
        }

        .text-center { text-align: center; }

        .footer-sigs { margin-top: 40px; display: flex; justify-content: space-between; }
        .sig-line { width: 28%; border-top: 1.5px solid #000; text-align: center; padding-top: 5px; font-size: 12px; }

        @media print { 
            .no-print { display: none; } 
            body { padding: 40px; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align:center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 24px; cursor: pointer; font-weight: bold;">PRINT SLIP</button>
    </div>

    <div class="slip-container">
        <div class="header-wrapper">
            <div class="logo-container">
                <img src="8thmile_logo.png" alt="8th Mile Logo">
            </div>
            <div class="company-info">
                <div class="company-name">8thmile Staffing and General Services INC.</div>
                <div class="company-address">2nd Floor Estrella Commercial Center, National Hi-way Brgy. Macabling Sta. Rosa, Laguna</div>
                <div class="title-box">ACCOUNTABILITY SLIP</div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-column">
                <div><span class="label">Ref:</span> <?php echo htmlspecialchars($trans_id); ?></div>
                <div><span class="label">Date:</span> <?php echo date('M d, Y', strtotime($first_row['date_out'])); ?></div>
            </div>
            <div class="info-column right">
                <div><span class="label">Holder:</span> <?php echo htmlspecialchars($first_row['holder_name']); ?></div>
                <div><span class="label">Site:</span> <?php echo htmlspecialchars($first_row['client_name']); ?></div>
                <div><span class="label">Project:</span> <?php echo htmlspecialchars($first_row['project_name'] ?? 'N/A'); ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="8%">NO.</th> 
                    <th width="10%">QTY</th>
                    <th width="12%">UNIT</th>
                    <th>DESCRIPTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $item_no = 1; 
                while($row = mysqli_fetch_assoc($result)): 
                ?>
                <tr>
                    <td class="text-center"><?php echo $item_no++; ?></td> 
                    <td class="text-center"><?php echo $row['quantity']; ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['unit'] ?? '-'); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($row['product_name']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="footer-sigs">
            <div class="sig-line">Receiver / Holder</div>
            <div class="sig-line">Authorized Issuer</div>
            <div class="sig-line">Security Officer</div>
        </div>
    </div>
</body>
</html>