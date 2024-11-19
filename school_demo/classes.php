<?php
include 'config.php';
include 'menu.php';
// Handle Add, Edit, and Delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_class'])) {
        // Add new class
        $name = $_POST['name'];
        $sql = "INSERT INTO classes (name) VALUES (:name)";
        $statement = $conn->prepare($sql);
        $statement->execute(['name' => $name]);
        header("Location: classes.php");
        exit;
    } elseif (isset($_POST['edit_class'])) {
        // Edit class
        $id = $_POST['id'];
        $name = $_POST['name'];
        $sql = "UPDATE classes SET name = :name WHERE class_id = :id";
        $statement = $conn->prepare($sql);
        $statement->execute(['name' => $name, 'id' => $id]);
        header("Location: classes.php");
        exit;
    }
}

if (isset($_GET['delete'])) {
    // Delete class
    $id = $_GET['delete'];
    $sql = "DELETE FROM classes WHERE class_id = :id";
    $statement = $conn->prepare($sql);
    $statement->execute(['id' => $id]);
    header("Location: classes.php");
    exit;
}

// Fetch class details for editing
$editClass = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM classes WHERE class_id = :id";
    $statement = $conn->prepare($sql);
    $statement->execute(['id' => $id]);
    $editClass = $statement->fetch(PDO::FETCH_ASSOC);
}

?>
<div class="container mt-4">
    <div class="row">
        <!-- Form for Add/Edit Class -->
        <div class="col-md-4">
            <h5><?php echo $editClass ? 'Edit Class' : 'Add New Class'; ?></h5>
            <form id="form" method="POST" action="">
                <input type="hidden" name="id" value="<?php echo $editClass['class_id'] ?? ''; ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Class Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $editClass['name'] ?? ''; ?>">
                    <div class="error-message" id="nameError"></div>
                </div>
                <button type="submit" class="btn btn-primary" name="<?php echo $editClass ? 'edit_class' : 'add_class'; ?>">
                    <?php echo $editClass ? 'Update Class' : 'Add Class'; ?>
                </button>
                <?php if ($editClass): ?>
                    <a href="classes.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>



        <div class="container mt-4">
            <div class="table-responsive">
                <h4 class="text-center">Classes List</h4>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Class Name</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM classes";
                        // Execute query
                        $statement = $conn->prepare($sql);

                        $statement->execute();

                        // Fetch all rows
                        $classes = $statement->fetchAll(PDO::FETCH_ASSOC);

                        // Check if there are any rows
                        if (count($classes) > 0) {
                            foreach ($classes as $class) {
                                // Get individual data
                                $id = $class['class_id'];
                                $name = $class['name'];
                                $created = $class['created_at'];
                        ?>

                                <tr>
                                    <td><?php echo $id; ?></td>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo $created; ?></td>
                                    <td>
                                        <a href="?edit=<?= $id; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="?delete=<?= $id; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            // If no category found
                            echo "<tr><td colspan='4'>No classes found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


        </body>

        </html>
        <script>
            // JavaScript for form validation
            const form = document.getElementById('form');

            form.addEventListener('submit', function(event) {
                let isValid = true;

                // Validate class 
                const nameInput = document.getElementById('name');
                const nameError = document.getElementById('nameError');
                if (!nameInput.value.trim()) {
                    nameInput.classList.add('error-input');
                    nameError.textContent = 'Class Name is required.';
                    isValid = false;
                } else if (!isValidName(nameInput.value.trim())) {
                    nameInput.classList.add('error-input');
                    nameError.textContent = 'Please enter a valid class name. (Ex: 10th A)';
                    isValid = false;
                } else {
                    nameInput.classList.remove('error-input');
                    nameError.textContent = '';
                }

                if (!isValid) {
                    event.preventDefault(); // Prevent form submission if not valid
                }
            });

            // Function to validate product format
            function isValidName(name) {
                // Regular expression for email validation
                const NameRegex = /^[A-Za-z0-9 ]{3}/;
                return NameRegex.test(name);
            }
        </script>