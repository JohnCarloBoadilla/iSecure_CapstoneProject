<?php
require_once 'auth_check.php';
require_once 'audit_log.php';

if (!isset($_SESSION['token'])) {
    header("Location: loginpage.php");
    exit;
}

$mode = $_GET['mode'] ?? 'face';

$fullName = 'Unknown User';
$role = 'Unknown Role';

// Validate token in DB and get session row
$stmt = $pdo->prepare("SELECT * FROM personnel_sessions WHERE token = :token AND expires_at > NOW()");
$stmt->execute([':token' => $_SESSION['token']]);
$session = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$session) {
    // Session expired or invalid
    session_unset();
    session_destroy();
    header("Location: loginpage.php");
    exit;
}

// Fetch user info using the user_id from the personnel_sessions row
if (!empty($session['user_id'])) {
    $stmt = $pdo->prepare("SELECT full_name, role FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $session['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // always sanitize output
        $fullName = htmlspecialchars($user['full_name'] ?? 'Unknown User', ENT_QUOTES, 'UTF-8');
        $role = htmlspecialchars($user['role'] ?? 'Unknown Role', ENT_QUOTES, 'UTF-8');
    } else {
        // user record missing — log out to be safe
        session_unset();
        session_destroy();
        header("Location: loginpage.php");
        exit;
    }
} else {
    // weird session row with no user_id — destroy and redirect
    session_unset();
    session_destroy();
    header("Location: loginpage.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../../images/logo/5thFighterWing-logo.png">
    <link rel="stylesheet" href="../../stylesheet/livefeed.css">
    <link rel="stylesheet" href="../../stylesheet/sidebar.css">
    <title>Live Feed</title>
</head>
<body>

<div class="body">

    <div class="left-panel">
        <div id="sidebar-container"></div>
    </div>

    <div class="right-panel">
        <div class="main-content">
        
            <div class="main-header">
                <div class="header-left">
                    <i class="fa-solid fa-home"></i> 
                    <h6 class="path"> / Dashboard /</h6>
                    <h6 class="current-loc">Live Feed</h6>
                </div>

                <div class="header-right">
                    <i class="fa-regular fa-bell me-3"></i>
                    <i class="fa-regular fa-message me-3"></i>

                    <div class="user-info">
                        <i class="fa-solid fa-user-circle fa-lg me-2"></i>
                        <div class="user-text">
                            <span class="username"><?php echo $fullName; ?></span>
                            <a id="logout-link" class="logout-link" href="logout.php">Logout</a>

                            <!-- Confirm Modal -->
                            <div id="confirmModal" class="modal">
                                <div class="modal-content">
                                    <p id="confirmMessage"></p>
                                    <div class="modal-actions">
                                        <button id="confirmYes" class="btn btn-danger">Yes</button>
                                        <button id="confirmNo" class="btn btn-secondary">No</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Camera section -->
            <div class="main-content">
                <h2>Camera Live Feed</h2>
                <img id="livefeed" width="640" height="480" alt="Live Feed">
                <div id="result">Waiting for detection...</div>
                <div id="actions">
                    <button id="captureBtn">Capture and Recognize</button>
                </div>
            </div>

        </div>
    </div>

</div>
<script>
    const mode = '<?php echo $mode; ?>';
</script>
<script src="../../scripts/sidebar.js"></script>
<script src="../../scripts/livefeed.js"></script>
<script src="../../scripts/session_check.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const img = document.getElementById("livefeed");
    const resultDiv = document.getElementById("result");
    const captureBtn = document.getElementById("captureBtn");

    // Live feed is streaming via MJPEG

    // Capture and send on button click
    captureBtn.addEventListener("click", async () => {
      resultDiv.innerText = "Capturing...";
      try {
        const response = await fetch('http://localhost:8000/camera/single_frame');
        const blob = await response.blob();

        const fd = new FormData();
        fd.append("image", blob, "capture.jpg");
        fd.append("mode", mode);

        const res = await fetch("process_recognition.php", { method: "POST", body: fd });
        const data = await res.json();
        resultDiv.innerText = JSON.stringify(data, null, 2);

        if(data.recognized){
            window.location.href = `personalinformation.php?id=${data.id}&type=${data.type}`;
        }
      } catch(err) {
        resultDiv.innerText = "Recognition failed: " + err.message;
      }
    });

    /* ---- Logout modal ---- */
    document.addEventListener("DOMContentLoaded", () => {
      const logoutLink = document.getElementById("logout-link");
      if (logoutLink) {
        logoutLink.addEventListener("click", (ev) => {
          ev.preventDefault();
          const modal = document.getElementById("confirmModal");
          const msgEl = document.getElementById("confirmMessage");
          const yes = document.getElementById("confirmYes");
          const no = document.getElementById("confirmNo");

          msgEl.textContent = "Are you sure you want to log out?";
          modal.classList.add("show");

          yes.onclick = () => { window.location.href = logoutLink.href; };
          no.onclick = () => { modal.classList.remove("show"); };
        });
      }
    });
</script>

</body>
</html>
