<?php
$conn = new mysqli("localhost", "root", "", "brg-pulo");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM `res-info` WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('Resident deleted successfully.'); window.location.href = 'admin.php';</script>";
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $conn->prepare("UPDATE `res-info` SET fname=?, mname=?, lname=?, age=?, dob=?, `c-status`=?, number=? WHERE id=?");
    $stmt->bind_param(
        "sssssssi",
        $_POST['fname'],
        $_POST['mname'],
        $_POST['lname'],
        $_POST['age'],
        $_POST['dob'],
        $_POST['c_status'],
        $_POST['number'],
        $_POST['edit_id']
    );
    $stmt->execute();
    echo "<script>alert('Resident updated successfully.'); window.location.href = 'admin.php';</script>";
    exit();
}

// If edit is triggered, get resident data
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM `res-info` WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $editData = $result->fetch_assoc();
    }
}

// Fetch all residents
$sql = "SELECT * FROM `res-info` ORDER BY purok, lname, fname";
$result = $conn->query($sql);
$residents = [];
$puroks = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
        if (!in_array($row['purok'], $puroks)) {
            $puroks[] = $row['purok'];
        }
    }
}
?>

<style>
/* ========== Layout & Modals ========== */
.purok-box {
    display: inline-block;
    background: #e0e0e0;
    padding: 10px;
    margin: 10px;
    border-radius: 8px;
    cursor: pointer;
    width: 150px;
    text-align: center;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    margin-left: 100px;
}

.modal-overlay.active {
    display: flex;
}

.modal-content {
    background: #fff;
    padding: 20px;
    max-height: 90vh;
    overflow-y: auto;
    width: 800px;
    border-radius: 10px;
    position: relative;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    cursor: pointer;
    font-weight: bold;
}

/* ========== Buttons ========== */
a.button, button {
    background-color: #007bff;
    color: white;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    transition: background-color 0.3s ease;
    display: inline-block;
}

a.button:hover, button:hover {
    background-color: #0056b3;
}

/* ========== Form Styling ========== */
.edit-form input {
    width: 100%;
    margin: 8px 0;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.edit-form h3 {
    margin-bottom: 15px;
}

/* ========== Responsive ========== */
@media screen and (max-width: 600px) {
    .modal-content {
        width: 90%;
        margin-right: 0;
    }

    a.button, button {
        font-size: 13px;
        padding: 8px 10px;
    }

    .purok-box {
        width: 100%;
        margin: 5px 0;
    }
}
</style>

<div class="resident-table-container">
    <h2>All Residents by Purok</h2>

    <?php foreach ($puroks as $purok): ?>
        <div class="purok-box" onclick="openPurokModal('modal_<?= $purok ?>')">
            <strong>Purok <?= htmlspecialchars($purok) ?></strong>
        </div>

        <div id="modal_<?= $purok ?>" class="modal-overlay">
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <h2>Residents in Purok <?= htmlspecialchars($purok) ?></h2>
                <table border="1" cellpadding="5" cellspacing="0" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>DOB</th>
                            <th>Status</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($residents as $person): ?>
                            <?php if ($person['purok'] == $purok): ?>
                                <tr>
                                    <td><img src="<?= $person['profile'] ?: 'images/sub/usericon.png' ?>" width="40" height="40" style="border-radius:50%; object-fit:cover;"></td>
                                    <td><?= htmlspecialchars($person['fname'] . ' ' . $person['mname'] . ' ' . $person['lname']) ?></td>
                                    <td><?= htmlspecialchars($person['age']) ?></td>
                                    <td><?= htmlspecialchars($person['dob']) ?></td>
                                    <td><?= htmlspecialchars($person['c-status']) ?></td>
                                    <td><?= htmlspecialchars($person['number']) ?></td>
                                    <td>
                                        <a href="admin.php?edit=<?= $person['id'] ?>" class="button">Edit</a>
                                        <a href="admin.php?delete=<?= $person['id'] ?>" class="button" onclick="return confirm('Delete this resident?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function openPurokModal(id) {
    document.getElementById(id).classList.add('active');
}

document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', function () {
        const modal = this.closest('.modal-overlay');
        modal.classList.remove('active');
    });
});

document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
</script>

<?php if ($editData): ?>
<!-- Floating Edit Modal -->
<div class="modal-overlay active" id="editModal">
    <div class="modal-content edit-form">
        <span class="modal-close" onclick="closeEditModal()">&times;</span>
        <h3>Edit Resident</h3>
        <form method="POST" action="admin.php">
            <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
            <input type="text" name="fname" value="<?= htmlspecialchars($editData['fname']) ?>" placeholder="First Name" required>
            <input type="text" name="mname" value="<?= htmlspecialchars($editData['mname']) ?>" placeholder="Middle Name" required>
            <input type="text" name="lname" value="<?= htmlspecialchars($editData['lname']) ?>" placeholder="Last Name" required>
            <input type="number" name="age" value="<?= htmlspecialchars($editData['age']) ?>" placeholder="Age" required>
            <input type="date" name="dob" value="<?= htmlspecialchars($editData['dob']) ?>" required>
            <input type="text" name="c_status" value="<?= htmlspecialchars($editData['c-status']) ?>" placeholder="Civil Status" required>
            <input type="text" name="number" value="<?= htmlspecialchars($editData['number']) ?>" placeholder="Contact Number" required>
            <button type="submit" class="button">Save Changes</button>
        </form>
    </div>
</div>

<script>
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    window.location.href = 'admin.php';
}
</script>
<?php endif; ?>
