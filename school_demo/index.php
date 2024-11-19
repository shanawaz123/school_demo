<?php
include 'config.php';
include 'menu.php';
// session for if category exists from update-category.php
if (isset($_SESSION['cat-exist'])) {
    ?>
      <!-- Modal for delete-stock $_SESSION from delete-product.php -->
      <div class="modal fade" id="catexistModal" tabindex="-1" role="dialog" aria-labelledby="catexistModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="catexistModalLabel">ALERT</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <?php
              // Display session message if set
              echo $_SESSION['cat-exist'];
              // Unset the session variable to ensure modal appears only once
              unset($_SESSION['cat-exist']);
              ?>
  
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Script to trigger the modal -->
      <script>
        $(document).ready(function() {
          $('#catexistModal').modal('show');
        });
      </script>
    <?php
    }
  
?>
<div class="container mt-4">
    <div class="table-responsive">
        <h4 class="text-center">Students List</h4>
        <a href="create.php" class="btn btn-primary mb-3">Add New Student</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT student.*, classes.name AS class_name 
                FROM student 
                LEFT JOIN classes ON student.class_id = classes.class_id";
                // Execute query
                $statement = $conn->prepare($sql);

                $statement->execute();

                // Fetch all rows
                $students = $statement->fetchAll(PDO::FETCH_ASSOC);

                // Check if there are any rows
                if (count($students) > 0) {
                    foreach ($students as $student) {
                        // Get individual data
                        $id = $student['id'];
                        $name = $student['name'];
                        $email = $student['email'];
                        $class_name = $student['class_name'];
                        $created = $student['created_at'];
                        $image = $student['image'];
                ?>

                        <tr>
                            <td><img src="uploads/<?php echo $image; ?>" width="55" height="50px"></td>
                            <td><?php echo $name; ?></td>
                            <td><?php echo $email; ?></td>
                            <td><?php echo $class_name; ?></td>
                            <td><?php echo $created; ?></td>
                            <td>
                                <a href="view.php?id=<?= $student['id'] ?>" class="btn btn-info btn-sm">View</a>
                                <a href="edit.php?id=<?= $student['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete.php?id=<?= $student['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    // If no category found
                    echo "<tr><td colspan='4'>No students found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>

</html>