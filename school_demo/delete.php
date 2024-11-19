<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if (isset($_POST['confirm'])) {
        // User confirmed deletion
        $stmt = $conn->prepare("SELECT image FROM student WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            if ($student['image']) {
                unlink("uploads/" . $student['image']);
            }

            $stmt = $conn->prepare("DELETE FROM student WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }

        header('Location: index.php');
    } elseif (isset($_POST['cancel'])) {
        // User canceled deletion
        header('Location: index.php');
    }
} else {
    echo "Invalid request.";
    exit;
}
?>

<?php include 'menu.php'; ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <h4>Confirm Deletion</h4>
            <p>Are you sure you want to delete this student?</p>
            <form method="POST">
                <button type="submit" name="confirm" class="btn btn-danger">Yes, Delete</button>
                <button type="submit" name="cancel" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
    </div>
</div>
</body>

</html>