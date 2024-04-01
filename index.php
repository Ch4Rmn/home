<?php
session_start();

require_once('./config/database.php');
require_once('./config/configuration.php');
require_once('./config/auth.php');
require_once('./include/include_function.php');
// echo $created_by;
// exit();


$name = $age = $password = $confirm_password = $gender = $townships = $hobby = $image = "";
$hobby = [];
$error = false;
$errorMessage = "";
$success = false;
$successMessage = "";
$delete_link = 'delete.php';
$edit_link = 'edit.php';

$townshipsSql = "SELECT * FROM `townships`";
$townshipsQuery = $mysqli->query($townshipsSql);

$hobbiesSql = 'SELECT * FROM `hobbies`';
$hobbies_res = $mysqli->query($hobbiesSql);

if (isset($_POST['Submit'])) {
    $name = $mysqli->real_escape_string($_POST['name']);
    $age = $mysqli->real_escape_string($_POST['age']);
    $gender = $mysqli->real_escape_string($_POST['gender']);
    $password = $mysqli->real_escape_string($_POST['password']);
    $confirm_password = $mysqli->real_escape_string($_POST['confirm_password']);
    $townships = $mysqli->real_escape_string($_POST['townships']);
    $hobby = (isset($_POST['hobby'])) ? $_POST['hobby'] : [];

    if ($name == '' || $age == '' || $password == '' || $townships == '' || empty($hobby) || $gender == '' || $password != $confirm_password || !is_numeric($age)) {
        $error = true;
        $errorMessage = "Please fill in all the required fields correctly.";
    } else {
        if ($hashPassword = isPasswordValid($password)) {
            $hashPassword = generatePass($password, $shaKey);
        }

        if (isset($_FILES['file'])) {
            $uploadDir = 'image/';
            if (!is_dir($uploadDir) || !file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $image_name = $uploadDir . uniqid() . date("d-m-y") . basename($_FILES['file']['name']);
            $tmp_name = $_FILES['file']['tmp_name'];

            if (checkImageExtension($image_name)) {
                move_uploaded_file($tmp_name, $image_name);
            }

            $sql = "INSERT INTO `user` (name,  age, password, gender, townships, image,created_by) VALUES ('$name','$age','$hashPassword','$gender','$townships','$image_name','$created_by')";

            if ($mysqli->query($sql)) {
                $user_id = $mysqli->insert_id;

                foreach ($hobby as $hobby_id) {
                    $userHobbies = "INSERT INTO `user_hobbies` (user_id, hobby_id,created_by) VALUES ('$user_id', '$hobby_id','$created_by')";
                    $mysqli->query($userHobbies);
                }

                $success = true;
                $successMessage = "<div style='color: green; border-style: dashed; text-align: center;'>Insert Complete!</div>";

                $name = $age = $password = $confirm_password = $gender = $townships = $hobby = $image = "";
                $hobby = [];
            } else {
                $error = true;
                $errorMessage = "<div style='color: red;border-style: dashed;text-align: center;'>Insert Fail!</div>";
            }
        }
    }
}

$userDatas = "SELECT 
    user.*, 
    townships.name AS township_name, 
    GROUP_CONCAT(DISTINCT hobbies.name) AS hobbies
FROM 
    `user` 
LEFT JOIN 
    `townships` ON user.townships = townships.id
LEFT JOIN 
    `user_hobbies` ON user.id = user_hobbies.user_id
LEFT JOIN 
    `hobbies` ON user_hobbies.hobby_id = hobbies.id
WHERE 
    user.deleted_at IS NULL
GROUP BY 
    user.id 
ORDER BY 
    id DESC;
";

$userData_res = $mysqli->query($userDatas);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        #th {
            background-color: #dddddd;
        }
    </style>
</head>

<body>
    <div class="container mt-3 pt-3">

        <h1>Form</h1>
        <?php if ($error) : ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <?php if ($success) : ?>
            <div class="alert alert-success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <form action="<?php echo $baseUrl ?>index.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="">Name</label>
                <input type="text" name="name" id="" value="<?php echo $name ?>"> <br />

                <label for="">Password</label>
                <input type="password" name="password" id="" value=""> <br />

                <label for="">Confirm Password</label>
                <input type="password" name="confirm_password" id="" value=""> <br />

                <label for="">Age</label>
                <input type="text" name="age" id="" value="<?php echo $age ?>"> <br />

                <select name="townships" class="form-control">
                    <option value="">Choose Your Township</option>
                    <?php while ($town = $townshipsQuery->fetch_assoc()) { ?>
                        <option value="<?php echo $town['id']; ?>"><?php echo $town['name'] ?></option>
                    <?php } ?>
                </select>
                <hr>

                <!-- radio with gender -->
                <label for="gender">Gender</label><br>
                <input type="radio" id="male" name="gender" value="1" <?php if ($gender == '1') echo 'checked'; ?>>
                <label for="male">Male</label><br>

                <input type="radio" id="female" name="gender" value="2" <?php if ($gender == '2') echo 'checked'; ?>>
                <label for="female">Female</label><br>
                <hr>

                <!-- hobbies  -->
                Hobbies
                <br>
                <?php while ($hobby_row = $hobbies_res->fetch_assoc()) { ?>
                    <input type="checkbox" name="hobby[]" id="hobby" value="<?php echo $hobby_row['id']; ?>" <?php if (in_array($hobby_row['id'], $hobby)) echo "checked"; ?>>
                    <label for="hobby"><?php echo $hobby_row['name'] ?></label><br>
                <?php } ?>

                <!-- Image upload -->
                <label for="file-upload">Upload Image</label>
                <input type="file" name='file' id="file-upload" accept="image/*" onchange="previewImage(event);" />
                <img id="preview-selected-image" style="display: none; max-width: 200px; margin-top: 10px;" />
                <input type="hidden" name="form-sub" value="<?php echo $user['id'] ?>"><br>
                <input style="color:green;" type="submit" name="Submit" value="Submit"><br><br>
                <a style="color:red;" href="<?php echo $baseUrl; ?>logout.php">Log Out</a>
            </div>
        </form>
        <hr>

        <!-- Display table -->
        <div>
            <h1>Login User Information</h1>
            <div>
                <h3>Session</h3>
                id - <?php echo $_SESSION['id']; ?> <br>
                name - <?php echo $_SESSION['name']; ?> <br>
                password - <?php echo $_SESSION['password']; ?> <br>
            </div>

            <div>
                <h3>Cookie</h3>
                id - <?php echo $_COOKIE['id']; ?> <br>
                name - <?php echo $_COOKIE['name']; ?> <br>
                password - <?php echo $_COOKIE['password']; ?> <br>
            </div>
            <hr>
            <h2>Table</h2>
            <table>
                <thead>
                    <tr id="th">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Township</th>
                        <th>Hobbies</th>
                        <th>Image</th>
                        <th>Created_at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($user = $userData_res->fetch_assoc()) {
                        $userID = $user['id'];
                    ?>
                        <tr>
                            <td><?php echo (int) $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo ($user['gender'] == 1) ? "Male" : "Female"; ?></td>
                            <td><?php echo htmlspecialchars($user['age']); ?></td>
                            <td><?php echo htmlspecialchars($user['township_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['hobbies']); ?></td>
                            <td><img src="<?php echo $baseUrl . htmlspecialchars($user['image']); ?>" id="preview-selected-image" style="width: 100px;height:100px;object-fit: cover;" /></td>
                            <td><?php echo $user['created_at'] ?></td>
                            <td>
                                <a href='<?php echo $baseUrl . $delete_link . "?id=" . $userID; ?>'>Delete</a>
                                <a href='<?php echo $baseUrl . $edit_link . "?id=" . $userID; ?>'>Edit</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    </form>

    <!-- show table -->

    </div>
</body>
<script>
    // Image preview
    const previewImage = (event) => {
        const imageFiles = event.target.files;
        const imagePreviewElement = document.querySelector("#preview-selected-image");
        const imageSrc = URL.createObjectURL(imageFiles[0]);
        if (imageSrc) {
            imagePreviewElement.src = imageSrc;
            imagePreviewElement.style.display = "block";
        }
    };
</script>

</html>