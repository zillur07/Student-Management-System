<?php

require_once __DIR__ . "/db.php";

$message = "";
$messageType = "";

if (isset($_POST["submit"])) {

    $name = trim($_POST["name"] ?? "");
    $roll = (int) ($_POST["roll"] ?? 0);
    $class = trim($_POST["class"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    if (
        $name === "" ||
        $roll <= 0 ||
        $class === "" ||
        $email === "" ||
        $phone === "" ||
        $address === ""
    ) {

        $message = "Please fill in all fields.";
        $messageType = "danger";

    } else {

        $imageName = null;

        // Image upload
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {

            $allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/webp"
            ];

            $fileType = mime_content_type($_FILES["image"]["tmp_name"]);

            if (!in_array($fileType, $allowedTypes)) {

                $message = "Only JPG, PNG and WEBP images are allowed.";
                $messageType = "danger";

            } elseif ($_FILES["image"]["size"] > 2 * 1024 * 1024) {

                $message = "Image size must be less than 2MB.";
                $messageType = "danger";

            } else {

                $extension = strtolower(
                    pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION)
                );

                $imageName = uniqid("student_", true) . "." . $extension;

                $uploadPath = __DIR__ . "/uploads/" . $imageName;

                if (!move_uploaded_file(
                    $_FILES["image"]["tmp_name"],
                    $uploadPath
                )) {

                    $message = "Failed to upload image.";
                    $messageType = "danger";
                    $imageName = null;
                }
            }
        }

        if ($message === "") {

            $sql = "INSERT INTO students
                    (name, roll, `class`, email, phone, address, image)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "sisssss",
                $name,
                $roll,
                $class,
                $email,
                $phone,
                $address,
                $imageName
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header("Location: index.php?success=1");
                exit;

            } else {

                $message = "Database error: " . mysqli_error($conn);
                $messageType = "danger";

                // Delete uploaded image if database insert fails
                if ($imageName !== null) {
                    $imagePath = __DIR__ . "/uploads/" . $imageName;

                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                mysqli_stmt_close($stmt);
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Student - Student Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">


<!-- Navbar -->

<nav class="navbar navbar-dark bg-dark shadow-sm">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand fw-bold"
        >
            🎓 Student Management
        </a>

        <a
            href="index.php"
            class="btn btn-outline-light"
        >
            Student List
        </a>

    </div>

</nav>


<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">


            <div class="card border-0 shadow">

                <div class="card-header bg-primary text-white py-3">

                    <h4 class="mb-0">
                        👨‍🎓 Add New Student
                    </h4>

                </div>


                <div class="card-body p-4">


                    <?php if ($message !== "") { ?>

                        <div class="alert alert-<?php echo $messageType; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>

                    <?php } ?>


                    <form
                        method="POST"
                        enctype="multipart/form-data"
                    >


                        <!-- Student Image -->

                        <div class="text-center mb-4">

                            <div
                                class="mb-3"
                                id="imagePreviewContainer"
                                style="display:none;"
                            >

                                <img
                                    id="imagePreview"
                                    src=""
                                    alt="Preview"
                                    class="rounded-circle border"
                                    style="
                                        width: 130px;
                                        height: 130px;
                                        object-fit: cover;
                                    "
                                >

                            </div>


                            <label class="form-label fw-semibold">
                                Student Photo
                            </label>

                            <input
                                type="file"
                                name="image"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp"
                                onchange="previewImage(event)"
                            >

                            <small class="text-muted">
                                JPG, PNG or WEBP. Maximum 2MB.
                            </small>

                        </div>


                        <div class="row">


                            <!-- Name -->

                            <div class="col-md-8 mb-3">

                                <label class="form-label">
                                    Student Name
                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Enter student name"
                                    required
                                >

                            </div>


                            <!-- Roll -->

                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Roll Number
                                </label>

                                <input
                                    type="number"
                                    name="roll"
                                    class="form-control"
                                    placeholder="Roll"
                                    min="1"
                                    required
                                >

                            </div>


                            <!-- Class -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Class
                                </label>

                                <input
                                    type="text"
                                    name="class"
                                    class="form-control"
                                    placeholder="Example: Class 10"
                                    required
                                >

                            </div>


                            <!-- Email -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="student@example.com"
                                    required
                                >

                            </div>


                            <!-- Phone -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Phone
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    placeholder="01XXXXXXXXX"
                                    required
                                >

                            </div>


                            <!-- Address -->

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Address
                                </label>

                                <input
                                    type="text"
                                    name="address"
                                    class="form-control"
                                    placeholder="Student address"
                                    required
                                >

                            </div>

                        </div>


                        <div class="d-flex gap-2 mt-3">

                            <button
                                type="submit"
                                name="submit"
                                class="btn btn-primary px-4"
                            >
                                Add Student
                            </button>

                            <a
                                href="index.php"
                                class="btn btn-secondary"
                            >
                                Cancel
                            </a>

                        </div>


                    </form>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

function previewImage(event) {

    const image = document.getElementById("imagePreview");

    const container = document.getElementById(
        "imagePreviewContainer"
    );

    if (event.target.files.length > 0) {

        image.src = URL.createObjectURL(
            event.target.files[0]
        );

        container.style.display = "block";

    }

}

</script>


</body>
</html>