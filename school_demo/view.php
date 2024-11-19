<?php
include 'config.php';
include 'menu.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT student.*, classes.name AS class_name 
        FROM student 
        LEFT JOIN classes ON student.class_id = classes.class_id 
        WHERE student.id = :id";
         $statement = $conn->prepare($sql);
         $statement->bindParam(':id', $id);
         $statement->execute();
         $student = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo "Student not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h4 class="text-center">View Student</h4>
            <div class="card">
                <div class="card-body">
                    <img src="uploads/<?= $student['image'] ?>" width="150" class="mb-3" alt="Student Image">
                    <p><strong>Name:</strong> <?= $student['name'] ?></p>
                    <p><strong>Email:</strong> <?= $student['email'] ?></p>
                    <p><strong>Address:</strong> <?= $student['address'] ?></p>
                    <p><strong>Class:</strong> <?= $student['class_name'] ?? 'Not Assigned' ?></p>
                    <p><strong>Created At:</strong> <?= $student['created_at'] ?></p>
                    <a href="index.php" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>