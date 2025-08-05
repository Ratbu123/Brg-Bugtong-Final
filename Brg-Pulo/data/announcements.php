<?php
$conn = new mysqli("localhost", "root", "", "brg-pulo");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["title"], $_POST["content"])) {
    $title = trim($conn->real_escape_string($_POST["title"]));
    $content = trim($conn->real_escape_string($_POST["content"]));

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        if ($stmt->execute()) {
            $message = "✅ Announcement posted successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    } else {
        $message = "⚠️ Both fields are required.";
    }
}

$result = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Announcements</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-100 via-white to-blue-200 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <h1 class="text-3xl font-bold text-center text-blue-900 mb-6">📢 Barangay Announcements</h1>

        <!-- Flash Message -->
        <?php if (!empty($message)): ?>
            <div id="message" class="bg-white border border-blue-300 text-blue-900 px-4 py-3 rounded shadow mb-6 text-center">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-10">
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold">Title</label>
                    <input type="text" name="title" required
                           class="w-full border border-gray-300 p-3 rounded-lg focus:ring focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold">Content</label>
                    <textarea name="content" rows="4" required
                              class="w-full border border-gray-300 p-3 rounded-lg focus:ring focus:ring-blue-300"></textarea>
                </div>
                <div class="text-center">
                    <button type="submit"
                            class="bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-800 transition-all">
                        ➕ Post Announcement
                    </button>
                </div>
            </form>
        </div>

        <!-- Announcements List -->
        <?php if ($result->num_rows > 0): ?>
            <div class="space-y-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white border-l-4 border-blue-600 p-4 rounded shadow-sm">
                        <h2 class="text-xl font-semibold text-blue-800"><?= htmlspecialchars($row['title']) ?></h2>
                        <p class="text-gray-700 mt-1"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                        <p class="text-sm text-gray-500 mt-2">🕒 Posted on <?= date("F j, Y, g:i a", strtotime($row['date_posted'])) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500">No announcements yet.</p>
        <?php endif; ?>
    </div>

    <script>
        // Auto-hide message
        setTimeout(() => {
            const msg = document.getElementById('message');
            if (msg) msg.style.display = 'none';
        }, 3000);
    </script>
</body>
</html>
