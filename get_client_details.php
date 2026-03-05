<?php
include('config.php');

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = mysqli_query($conn, "SELECT * FROM clients WHERE id = $id");
    $row = mysqli_fetch_assoc($res);

    if($row) {
        ?>
        <input type="hidden" name="client_id" value="<?php echo $row['id']; ?>">
        
        <div class="mb-3">
            <label class="form-label fw-bold">Company / Client Name</label>
            <input type="text" name="client_name" class="form-control" value="<?php echo htmlspecialchars($row['client_name'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Contact Person</label>
            <input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars($row['contact_person'] ?? ''); ?>">
        </div>

        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Email <span class="text-muted fw-normal">(Optional)</span></label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>">
            </div>
            <div class="col-6 mb-3">
                <label class="form-label fw-bold">Phone Number <span class="text-muted fw-normal">(Optional)</span></label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($row['phone'] ?? ''); ?>">
            </div>
        </div>

        <div class="mb-0">
            <label class="form-label fw-bold">Business Address <span class="text-muted fw-normal">(Optional)</span></label>
            <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($row['address'] ?? ''); ?></textarea>
        </div>
        <?php
    }
}
?>