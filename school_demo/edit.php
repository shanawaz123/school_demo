<?php
include 'config.php';
include 'menu.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute SELECT query
    $sql = "SELECT * FROM student WHERE id=:id";
    $statement = $conn->prepare($sql);
    $statement->bindParam(':id', $id);
    $statement->execute();
    $project = $statement->fetch(PDO::FETCH_ASSOC);

    if ($project) {
        $name = $project['name'];
        $email = $project['email'];
        $address = $project['address'];
        $class = $project['class_id'];
        $image = $project['image'];
    } else {
        //$_SESSION['category-notfound'] = "<div>Project not found</div>";
        header("Location:index.php ");
        exit; // Added exit after header redirect
    }
} else {
    header("Location:index.php ");
    exit; // Added exit after header redirect
}
$sql_classes = "SELECT class_id, name FROM classes";
$statement_classes = $conn->prepare($sql_classes);
$statement_classes->execute();
$classes = $statement_classes->fetchAll(PDO::FETCH_ASSOC);

//form submit
if (isset($_POST['submit'])) {
    // Get the values from the form
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $class_id = $_POST['class_id'];
    $address = $_POST['address'];
    $current_image = $_POST['current_image'];

    // Handle image upload
    if (isset($_FILES['new_Image']['name'])) {
        $new_Image = $_FILES['new_Image']['name'];
        if ($new_Image != "") {
            $image_name_parts = explode('.', $new_Image);
            $ext = end($image_name_parts);
            // Generate a unique image name
            $new_Image = 'stu_img_' . time() . '_' . uniqid() . '.' . $ext;
            $source_path = $_FILES['new_Image']['tmp_name'];
            $destination_path = 'uploads/' . $new_Image;
            $upload = move_uploaded_file($source_path, $destination_path);

            if (!$upload) {
                $_SESSION['upload-new_Image'] = "<div>Failed to upload new image</div>";
                header("Location: index.php");
                exit;
            }

            if ($current_image !== "") {
                $remove_path = "uploads/" . $current_image;
                $remove = unlink($remove_path);
                if (!$remove) {
                    $_SESSION['failed-remove-current_image'] = "<div>Failed to remove current image</div>";
                    header("Location: edit.php");
                    exit;
                }
            }
        } else {
            $new_Image = $current_image;
        }
    } else {
        $new_Image = $current_image;
    }



    //update category
    $sql2 = "UPDATE student SET
            name = :name,
            email = :email,
            address = :address,
            class_id = :class,
            image = :new_Image
            WHERE id = :id";

    $statement2 = $conn->prepare($sql2);
    $statement2->bindParam(':id', $id);
    $statement2->bindParam(':name', $name);
    $statement2->bindParam(':email', $email);
    $statement2->bindParam(':address', $address);
    $statement2->bindParam(':class', $class_id);
    $statement2->bindParam(':new_Image', $new_Image);
    $statement2->execute();

    if ($statement2->rowCount() > 0) {
        $_SESSION['update-cat'] = "Project Updated Successfully";
        header("Location:index.php");
        exit;
    } else {
        $_SESSION['update-cat'] = "Project Not Updated, Try Again..";
        header("Location:index.php ");
        exit;
    }
}

?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h4 class="text-center">Edit Student</h4>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control"><?php echo $address; ?></textarea>
                </div>
                <div class="form-group">
                    <label>Class</label>
                    <select name="class_id" class="form-control">
                        <?php
                        foreach ($classes as $class) {
                            $class_id = $class['class_id'];
                            $name = $class['name'];
                            $selected = ($class_id == $class) ? "selected" : "";
                            echo "<option value='$class_id' $selected>$name</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col">
                        <div class="form-group mt-3 mb-2">

                            <label for="images">Current Image:</label>
                            <?php
                            if ($image !== "") {
                                //display the image
                            ?>
                                <img src="uploads/<?php echo $image; ?>" alt="" id="current-image" class="imagePreview">
                            <?php
                            } else {
                                echo "<div>Image not Added</div>";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group mt-2 mb-3">
                            <input type="file" class="form-control file-input" id="new_Image" name="new_Image" onchange="previewImage()">
                            <div id="imagePreview"></div>
                        </div>

                    </div>
                </div>
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="current_image" value="<?php echo $image; ?>">
                <button type="submit" name="submit" class="btn btn-primary w-100 mt-2 mb-2">Update Student</button>
            </form>
        </div>
    </div>
</div>
<script>
    function previewImage() {
        const fileInput = document.getElementById('new_Image');
        const imagePreview = document.getElementById('imagePreview');

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function() {
                imagePreview.innerHTML = `<img src="${reader.result}" alt="Preview Image" class="imagePreview2">`;
            }
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
</script>

</body>

</html>