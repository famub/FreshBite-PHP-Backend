<?php
session_start();

// التحقق من أن المستخدم admin
if (!isset($_SESSION['userID']) || $_SESSION['userType'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = intval($_POST['report_id']);
    $owner_id = intval($_POST['owner_id']);
    $action = $_POST['action'];

    // بدء transaction
    mysqli_begin_transaction($conn);

    try {
        if ($action === 'block') {
            // 1. حذف جميع وصفات المستخدم
            //    (لأن في CASCADE، حذف الـ user كفاية)
            
            // 2. إضافة المستخدم إلى blockeduser
            $query = "SELECT firstName, lastName, emailAddress FROM user WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $owner_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($user) {
                $insert = "INSERT INTO blockeduser (firstName, lastName, emailAddress) VALUES (?, ?, ?)";
                $stmt2 = mysqli_prepare($conn, $insert);
                mysqli_stmt_bind_param($stmt2, "sss", $user['firstName'], $user['lastName'], $user['emailAddress']);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);

                // 3. حذف المستخدم من user
                $delete = "DELETE FROM user WHERE id = ?";
                $stmt3 = mysqli_prepare($conn, $delete);
                mysqli_stmt_bind_param($stmt3, "i", $owner_id);
                mysqli_stmt_execute($stmt3);
                mysqli_stmt_close($stmt3);
            }
        }

        // 4. حذف التقرير
        $delete_report = "DELETE FROM report WHERE id = ?";
        $stmt4 = mysqli_prepare($conn, $delete_report);
        mysqli_stmt_bind_param($stmt4, "i", $report_id);
        mysqli_stmt_execute($stmt4);
        mysqli_stmt_close($stmt4);

        mysqli_commit($conn);

        // التوجيه لصفحة الأدمن
        header("Location: admin_page.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "حدث خطأ: " . $e->getMessage();
    }
} else {
    header("Location: admin_page.php");
    exit();
}
?>