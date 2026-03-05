<?php
session_start();
include('config.php');

// Set Timezone
date_default_timezone_set('Asia/Manila'); 

// 1. Validation: Ensure ID exists in the URL
if(!isset($_GET['return_id']) || empty($_GET['return_id'])) {
    die("<div style='padding:20px; font-family:sans-serif; color:red;'>
            <strong>Error:</strong> No Return ID provided. 
            <br>Usage: print_return_slip.php?return_id=YOUR_ID
         </div>");
}

$return_id = mysqli_real_escape_string($conn, $_GET['return_id']);

// 2. Query execution: Fetch all items matching the return_id
$query = "SELECT r.*, p.name AS product_name, p.sku 
          FROM returns r 
          LEFT JOIN products p ON r.product_id = p.productID 
          WHERE r.return_id = '$return_id'";

$result = mysqli_query($conn, $query);

// 3. Check for database errors or empty sets
if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    die("<div style='padding:20px; font-family:sans-serif;'>
            <strong>No Record Found:</strong> The Return ID '$return_id' does not exist in the database.
         </div>");
}

// 4. Prepare data for display (fetch first row for header details)
$first_row = mysqli_fetch_assoc($result);
mysqli_data_seek($result, 0); // Reset pointer so the while loop can show all items below
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Slip - <?php echo htmlspecialchars($return_id); ?></title>
    <style>
        /* Minimized layout for 8th Mile Inventory System */
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 10px; color: #333; line-height: 1.3; }
        
        /* Positioning required to contain the absolute watermark */
        .slip-box { border: 1px solid #ccc; padding: 15px 25px; max-width: 800px; margin: auto; background: #fff; position: relative; z-index: 1; overflow: hidden; }
        
        /* Styling for the transparent watermark logo */
        .watermark-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 350px;
            opacity: 0.1; /* Makes it look transparent/faded */
            z-index: -1; /* Places it behind the text */
            pointer-events: none; 
        }

        .header { text-align: center; border-bottom: 2px solid #002d72; padding-bottom: 8px; margin-bottom: 12px; }
        .logo { font-size: 20px; font-weight: bold; color: #002d72; text-transform: uppercase; }
        .title { font-size: 14px; margin-top: 2px; font-weight: 600; letter-spacing: 1px; color: #d9534f; } /* Red tint to indicate return */
        
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .info-col { width: 48%; }
        .label { font-weight: bold; font-size: 10px; color: #666; text-transform: uppercase; }
        .value-text { color: #000; font-size: 13px; font-weight: 500; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f4f6f8; text-align: left; padding: 6px 8px; font-size: 11px; border: 1px solid #ddd; }
        td { padding: 4px 8px; border: 1px solid #eee; font-size: 12px; }
        
        .terms { font-size: 10px; color: #666; font-style: italic; margin-bottom: 20px; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; text-align: center; }
        .sig-box { width: 30%; border-top: 1px solid #333; padding-top: 5px; font-size: 11px; }
        
        /* Condition Badges */
        .cond-good { color: #198754; font-weight: bold; }
        .cond-fair { color: #ffc107; font-weight: bold; }
        .cond-damaged { color: #dc3545; font-weight: bold; }

        /* Forces the browser to remove default headers (Date, Title, URL, Page Number) */
        @page { margin: 0; }

        @media print {
            body { 
                margin: 1.5cm; /* Adds a clean margin back inside the printed page */
                padding: 0; 
            }
            .slip-box { 
                border: none; 
                margin: 0;
                padding: 0;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div style="text-align:center; margin-bottom: 10px;" class="no-print">
        <button onclick="window.print()" style="padding: 5px 15px; cursor: pointer; background: #002d72; color: white; border: none; border-radius: 3px;">Print Return Slip</button>
    </div>

    <div class="slip-box">
        <img src="8thmile_logo.png" class="watermark-logo" alt="Watermark Background">

        <div class="header">
            <div class="logo">8th Mile Staffing & General Services</div>
            <div class="title">RETURN ACKNOWLEDGEMENT SLIP</div>
        </div>

        <div class="info-grid">
            <div class="info-col">
                <div class="label">Return ID: <span class="value-text"><?php echo htmlspecialchars($return_id); ?></span></div>
                <div class="label">Date Returned: <span class="value-text"><?php echo date('M d, Y', strtotime($first_row['return_date'])); ?></span></div>
            </div>
            <div class="info-col" style="text-align: right;">
                <div class="label">Returned By: <span class="value-text"><?php echo htmlspecialchars($first_row['item_holder']); ?></span></div>
                <div class="label">Status: <span class="value-text" style="color: #198754;">Received</span></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="15%">SKU</th>
                    <th width="50%">Item Description</th>
                    <th width="20%">Condition</th>
                    <th width="15%" style="text-align: center;">Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    // Determine CSS class for condition
                    $cond_class = '';
                    if($row['item_condition'] == 'Good') $cond_class = 'cond-good';
                    if($row['item_condition'] == 'Fair') $cond_class = 'cond-fair';
                    if($row['item_condition'] == 'Damaged') $cond_class = 'cond-damaged';
                ?>
                <tr>
                    <td style="color: #555;"><?php echo htmlspecialchars($row['sku'] ?? 'N/A'); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['product_name'] ?? 'Unknown Item'); ?></strong></td>
                    <td class="<?php echo $cond_class; ?>"><?php echo htmlspecialchars($row['item_condition']); ?></td>
                    <td style="text-align: center; font-weight: bold;"><?php echo $row['quantity']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p class="terms">
        </p>

    
    </div>
</body>
</html>