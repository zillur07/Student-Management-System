<?php

require_once __DIR__ . "/db.php";

$sql = "SELECT * FROM students ORDER BY id DESC";

$result = mysqli_query($conn, $sql);

$totalStudents = mysqli_num_rows($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Student Management System</title>

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
            href="add.php"
            class="btn btn-primary"
        >
            + Add Student
        </a>

    </div>

</nav>


<div class="container py-5">


    <!-- Header -->

    <div class="row mb-4">

        <div class="col-md-8">

            <h2 class="fw-bold">
                Student Dashboard
            </h2>

            <p class="text-muted">
                Manage student information easily.
            </p>

        </div>


        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Total Students
                    </small>

                    <h2 class="fw-bold text-primary mb-0">
                        <?php echo $totalStudents; ?>
                    </h2>

                </div>

            </div>

        </div>

    </div>


    <!-- Alerts -->


    <?php if (isset($_GET["success"])) { ?>

        <div class="alert alert-success alert-dismissible fade show">

            ✅ Student added successfully!

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php } ?>


    <?php if (isset($_GET["updated"])) { ?>

        <div class="alert alert-success alert-dismissible fade show">

            ✅ Student updated successfully!

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php } ?>


    <?php if (isset($_GET["deleted"])) { ?>

        <div class="alert alert-success alert-dismissible fade show">

            ✅ Student deleted successfully!

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    <?php } ?>


    <!-- Student Table -->

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-bold">
                    All Students
                </h5>

                <a
                    href="add.php"
                    class="btn btn-sm btn-primary"
                >
                    + Add Student
                </a>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">


                    <thead class="table-dark">

                        <tr>

                            <th class="text-center">
                                SL
                            </th>

                            <th>
                                Photo
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Roll
                            </th>

                            <th>
                                Class
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Address
                            </th>

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php

                    $serial = 1;

                    if ($totalStudents > 0) {

                        while ($student = mysqli_fetch_assoc($result)) {

                    ?>


                        <tr>


                            <!-- Serial -->

                            <td class="text-center fw-bold">

                                <?php echo $serial; ?>

                            </td>


                            <!-- Image -->

                            <td>

                                <?php if (!empty($student["image"])) { ?>

                                    <img
                                        src="uploads/<?php echo htmlspecialchars($student["image"]); ?>"
                                        alt="Student"
                                        class="rounded-circle border"
                                        style="
                                            width:55px;
                                            height:55px;
                                            object-fit:cover;
                                        "
                                    >

                                <?php } else { ?>

                                    <div
                                        class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                        style="
                                            width:55px;
                                            height:55px;
                                        "
                                    >
                                        👤
                                    </div>

                                <?php } ?>

                            </td>


                            <!-- Name -->

                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $student["name"]
                                    );
                                    ?>

                                </strong>

                            </td>


                            <!-- Roll -->

                            <td>

                                <span class="badge bg-primary">

                                    <?php
                                    echo $student["roll"];
                                    ?>

                                </span>

                            </td>


                            <!-- Class -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $student["class"]
                                );
                                ?>

                            </td>


                            <!-- Email -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $student["email"]
                                );
                                ?>

                            </td>


                            <!-- Phone -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $student["phone"]
                                );
                                ?>

                            </td>


                            <!-- Address -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $student["address"]
                                );
                                ?>

                            </td>


                            <!-- Action -->

                            <td>

                                <div class="d-flex gap-1">

                                    <a
                                        href="edit.php?id=<?php echo $student["id"]; ?>"
                                        class="btn btn-sm btn-warning"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        href="delete.php?id=<?php echo $student["id"]; ?>"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this student?');"
                                    >
                                        Delete
                                    </a>

                                </div>

                            </td>


                        </tr>


                    <?php

                            $serial++;
                        }

                    } else {

                    ?>


                        <tr>

                            <td
                                colspan="9"
                                class="text-center py-5"
                            >

                                <h5 class="text-muted">
                                    No Students Found
                                </h5>

                                <a
                                    href="add.php"
                                    class="btn btn-primary mt-2"
                                >
                                    + Add First Student
                                </a>

                            </td>

                        </tr>


                    <?php } ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>
</html>