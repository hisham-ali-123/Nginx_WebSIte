<?php
$conn = pg_connect("host=localhost dbname=test1 user=postgres password=postgres");

if (!$conn) {
	die("Connection failed: " . pg_last_error());
	}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $title = $_POST['title'];
    $query = "INSERT INTO tasks (title) VALUES ($1)";
    $result = pg_query_params($conn, $query, [$title]);

    if (!$result) {
        echo "Error adding task: " . pg_last_error($conn);
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_task'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $completed = isset($_POST['completed']) ? 'true' : 'false';
    $query = "UPDATE tasks SET title = $1, completed = $2 WHERE id = $3";
    $result = pg_query_params($conn, $query, [$title, $completed, $id]);

    if (!$result) {
        echo "Error updating task: " . pg_last_error($conn);
    }
}


if (isset($_GET['delete_task'])) {
    $id = $_GET['delete_task'];
    $query = "DELETE FROM tasks WHERE id = $1";
    $result = pg_query_params($conn, $query, [$id]);

    if (!$result) {
        echo "Error deleting task: " . pg_last_error($conn);
    }
}



$query = "SELECT * FROM tasks";
$result = pg_query($conn, $query);

if (!$result) {
    echo "Error fetching tasks: " . pg_last_error($conn);
} else {
    $tasks = pg_fetch_all($result);
}

pg_close($conn);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
</head>
<body>
    <h1>To-Do List</h1>
    
    <form method="POST">
        <input type="text" name="title" placeholder="Add a new task">
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <ul>
        <?php if (!empty($tasks)): ?>
            <?php foreach ($tasks as $task): ?>
                <li>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?= $task['id'] ?>">
                        <input type="text" name="title" value="<?= $task['title'] ?>">
                        <input type="checkbox" name="completed" <?= $task['completed'] == 't' ? 'checked' : '' ?>>
                        <button type="submit" name="update_task">Update</button>
                    </form>
                    <a href="?delete_task=<?= $task['id'] ?>">Delete</a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tasks found.</p>
        <?php endif; ?>
    </ul>
</body>
</html>
