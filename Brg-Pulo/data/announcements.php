<?php
// Start the session and connect to the database

$conn = new mysqli("localhost", "root", "", "brg-pulo");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle announcement form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $conn->real_escape_string($_POST["title"]);
    $content = $conn->real_escape_string($_POST["content"]);

    $sql = "INSERT INTO announcements (title, content) VALUES ('$title', '$content')";
    if ($conn->query($sql) === TRUE) {
        $message = "Announcement posted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements</title>
    <style>

        h2 {
            color: #333;
        }   

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            margin: 10px 0;
            color: green;
        }

        .announcement {
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 6px;
        }

        .announcement h3 {
            margin: 0;
            color: #333;
        }

        .announcement p {
            margin: 5px 0 0;
        }

        .date {
            font-size: 0.9em;
            color: #888;
        }
    </style>
</head>
<body>

<h2>Post a New Announcement</h2>

<?php if ($message): ?>
    <p class="message"><?= $message ?></p>
<?php endif; ?>

<form method="POST" action="">
    <input type="text" name="title" placeholder="Announcement Title" required>
    <textarea name="content" rows="5" placeholder="Write your announcement here..." required></textarea>
    <button type="submit">Post Announcement</button>
</form>

<h2>Recent Announcements</h2>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="announcement">
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
        <div class="date">Posted on <?= date("F j, Y, g:i a", strtotime($row['date_posted'])) ?></div>
    </div>
<?php endwhile; ?>

</body>
</html>
