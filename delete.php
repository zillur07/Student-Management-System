<?php

require_once __DIR__ . "/db.php";

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($id <= 0) {
    die("Invalid student ID.");
}


// Get image name first

$sql = "SELECT image FROM students WHERE id = ?";

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


// Delete student

$sql = "DELETE FROM students WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);


    // Delete student image

    if (!empty($student["image"])) {

        $imagePath =
            __DIR__
            . "/uploads/"
            . $student["image"];

        if (file_exists($imagePath)) {

            unlink($imagePath);

        }
    }


    header("Location: index.php?deleted=1");
    exit;


} else {

    echo "Error: " . mysqli_error($conn);

    mysqli_stmt_close($stmt);
}

?>