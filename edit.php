<?php

require_once __DIR__ . "/db.php";

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($id <= 0) {
    die("Invalid student ID.");
}


// Get student

$sql = "SELECT * FROM students WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$student) {
    die("Student not found.");
}


$message = "";


// Update

if (isset($_POST["update"])) {

    $name = trim($_POST["name"] ?? "");
    $roll = (int) ($_POST["roll"] ?? 0);
    $class = trim($_POST["class"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    $newImageName = $student["image"];


    // New image upload

    if (
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] === UPLOAD_ERR_OK
    ) {

        $allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];

        $fileType = mime_content_type(
            $_FILES["image"]["tmp_name"]
        );


        if (!in_array($fileType, $allowedTypes)) {

            $message = "Only JPG, PNG and WEBP images are allowed.";

        } elseif ($_FILES["image"]["size"] > 2 * 1024 * 1024) {

            $message = "Image size must be less than 2MB.";

        } else {

            $extension = strtolower(
                pathinfo(
                    $_FILES["image"]["name"],
                    PATHINFO_EXTENSION
                )
            );

            $newImageName =
                uniqid("student_", true)
                . "."
                . $extension;


            $uploadPath =
                __DIR__
                . "/uploads/"
                . $newImageName;


            if (!move_uploaded_file(
                $_FILES["image"]["tmp_name"],
                $uploadPath
            )) {

                $message = "Failed to upload image.";
                $newImageName = $student["image"];

            } else {

                // Delete old image

                if (!empty($student["image"])) {

                    $oldImage =
                        __DIR__
                        . "/uploads/"
                        . $student["image"];

                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
            }
        }
    }


    if ($message === "") {

        $sql = "UPDATE students SET
                name = ?,
                roll = ?,
                `class` = ?,
                email = ?,
                phone = ?,
                address = ?,
                image = ?
                WHERE id = ?";


        $stmt = mysqli_prepare($conn, $sql);


        mysqli_stmt_bind_param(
            $stmt,
            "sisssssi",
            $name,
            $roll,
            $class,
            $email,
            $phone,
            $address,
            $newImageName,
            $id
        );


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header("Location: index.php?updated=1");
            exit;

        } else {

            $message =
                "Database error: "
                . mysqli_error($conn);

            mysqli_stmt_close($stmt);
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

    <title>Edit Student</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body class="bg-light">


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


                <div class="card-header bg-warning py-3">

                    <h4 class="mb-0">
                        ✏️ Edit Student
                    </h4>

                </div>


                <div class="card-body p-4">


                    <?php if ($message !== "") { ?>

                        <div class="alert alert-danger">

                            <?php
                            echo htmlspecialchars($message);
                            ?>

                        </div>

                    <?php } ?>


                    <form
                        method="POST"
                        enctype="multipart/form-data"
                    >


                        <!-- Current Image -->

                        <div class="text-center mb-4">

                            <?php if (!empty($student["image"])) { ?>

                                <img
                                    src="uploads/<?php echo htmlspecialchars($student["image"]); ?>"
                                    id="imagePreview"
                                    class="rounded-circle border shadow-sm mb-3"
                                    style="
                                        width:130px;
                                        height:130px;
                                        object-fit:cover;
                                    "
                                >

                            <?php } else { ?>

                                <div
                                    id="noImage"
                                    class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto mb-3"
                                    style="
                                        width:130px;
                                        height:130px;
                                        font-size:40px;
                                    "
                                >
                                    👤
                                </div>

                                <img
                                    id="imagePreview"
                                    src=""
                                    class="rounded-circle border shadow-sm mb-3"
                                    style="
                                        width:130px;
                                        height:130px;
                                        object-fit:cover;
                                        display:none;
                                    "
                                >

                            <?php } ?>


                            <div>

                                <label class="form-label fw-semibold">
                                    Change Photo
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
                                    value="<?php echo htmlspecialchars($student["name"]); ?>"
                                    required
                                >

                            </div>


                            <!-- Roll -->

                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Roll
                                </label>

                                <input
                                    type="number"
                                    name="roll"
                                    class="form-control"
                                    value="<?php echo $student["roll"]; ?>"
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
                                    value="<?php echo htmlspecialchars($student["class"]); ?>"
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
                                    value="<?php echo htmlspecialchars($student["email"]); ?>"
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
                                    value="<?php echo htmlspecialchars($student["phone"]); ?>"
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
                                    value="<?php echo htmlspecialchars($student["address"]); ?>"
                                    required
                                >

                            </div>


                        </div>


                        <div class="mt-3">

                            <button
                                type="submit"
                                name="update"
                                class="btn btn-warning px-4"
                            >
                                Update Student
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
    const noImage = document.getElementById("noImage");

    if (event.target.files.length > 0) {

        image.src = URL.createObjectURL(
            event.target.files[0]
        );

        image.style.display = "inline-block";

        if (noImage) {
            noImage.style.display = "none";
        }
    }

}

</script>


</body>
</html>