<?php
include 'config.php';
include 'menu.php';

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $class_id = $_POST['class_id'];

    // Image upload handling
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
        $image_name = $_FILES['image']['name'];
        $image_name_parts = explode('.', $image_name);
        $ext = end($image_name_parts);
        $image_name = 'stu_img_' . time() . '_' . uniqid() . '.' . $ext;
        $source_path = $_FILES['image']['tmp_name'];
        $destination_path = 'uploads/' . $image_name;

        $upload = move_uploaded_file($source_path, $destination_path);

        if (!$upload) {
            header('location:create.php');
            exit();
        }
    } else {
        $image_name = '';
    }

    $sql = "INSERT INTO student (name, email, address, class_id, image) VALUES (:name, :email, :address, :class, :image)";
    $statement = $conn->prepare($sql);
    $statement->bindParam(':name', $name);
    $statement->bindParam(':email', $email);
    $statement->bindParam(':address', $address);
    $statement->bindParam(':class', $class_id);
    $statement->bindParam(':image', $image_name);
    $statement->execute();

    header('location:index.php');
    exit();
}

$sql_classes = "SELECT class_id, name FROM classes";
$statement_classes = $conn->prepare($sql_classes);
$statement_classes->execute();
$classes = $statement_classes->fetchAll(PDO::FETCH_ASSOC);
?>
        
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h4 class="text-center">Add Student</h4>
            <form id="form" action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" id="name" class="form-control">
                    <div class="error-message" id="nameError"></div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input name="email" id="email" class="form-control">
                    <div class="error-message" id="emailError"></div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" id="address" class="form-control"></textarea>
                    <div class="error-message" id="addressError"></div>
                </div>
                <div class="form-group">
                    <label>Class</label>
                    <select name="class_id" id="class_id" class="form-control">
                        <option value="" selected disabled>Select Class</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['class_id'] ?>"><?= $class['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error-message" id="classError"></div>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" id="image" class="form-control" onchange="previewImage()">
                    <div id="imagePreview"></div>
                    <div class="error-message" id="imageError"></div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary w-100 mt-2">Add Student</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('form');

        form.addEventListener('submit', function (event) {
            let isValid = true;

            // Validate Name
            const nameInput = document.getElementById('name');
            const nameError = document.getElementById('nameError');
            const nameValue = nameInput.value.trim();

            if (!nameValue) {
                nameError.textContent = 'Name is required.';
                nameInput.classList.add('error-input');
                isValid = false;
            } else if (!/^[A-Za-z\s]{3,}$/.test(nameValue)) {
                nameError.textContent = 'Name should have at least 3 characters and contain only letters.';
                nameInput.classList.add('error-input');
                isValid = false;
            } else {
                nameError.textContent = '';
                nameInput.classList.remove('error-input');
            }

            // Validate Email
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            const emailValue = emailInput.value.trim();

            if (!emailValue) {
                emailError.textContent = 'Email is required.';
                emailInput.classList.add('error-input');
                isValid = false;
            } else if (!/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(emailValue)) {
                emailError.textContent = 'Enter a valid email.';
                emailInput.classList.add('error-input');
                isValid = false;
            } else {
                emailError.textContent = '';
                emailInput.classList.remove('error-input');
            }

            // Validate Address
            const addressInput = document.getElementById('address');
            const addressError = document.getElementById('addressError');
            const addressValue = addressInput.value.trim();

            if (!addressValue) {
                addressError.textContent = 'Address is required.';
                addressInput.classList.add('error-input');
                isValid = false;
            } else if (addressValue.length < 5) {
                addressError.textContent = 'Address should have at least 5 characters.';
                addressInput.classList.add('error-input');
                isValid = false;
            } else {
                addressError.textContent = '';
                addressInput.classList.remove('error-input');
            }

            // Validate Class
            const classInput = document.getElementById('class_id');
            const classError = document.getElementById('classError');
            const classValue = classInput.value;

            if (!classValue) {
                classError.textContent = 'Please select a class.';
                classInput.classList.add('error-input');
                isValid = false;
            } else {
                classError.textContent = '';
                classInput.classList.remove('error-input');
            }

            // Validate Image
            const imageInput = document.getElementById('image');
            const imageError = document.getElementById('imageError');
            const imageValue = imageInput.value;

            if (!imageValue) {
                imageError.textContent = 'Image is required.';
                imageInput.classList.add('error-input');
                isValid = false;
            } else if (!/\.(jpg|jpeg|png)$/i.test(imageValue)) {
                imageError.textContent = 'Image must be a JPG or PNG file.';
                imageInput.classList.add('error-input');
                isValid = false;
            } else {
                imageError.textContent = '';
                imageInput.classList.remove('error-input');
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    });

    function previewImage() {
        const fileInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function () {
                imagePreview.innerHTML = `<img src="${reader.result}" alt="Preview Image" class="imagePreview">`;
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
</script>
</body>
</html>
