<?php
class Node {
    public $data;
    public $left;
    public $right;

    public function __construct($data) {
        $this->data = $data;
        $this->left = null;
        $this->right = null;
    }
}

$jsonPath = __DIR__ . "/books.json";
if (!file_exists($jsonPath)) {
    die("Error: books.json file not found at $jsonPath");
}

$books = json_decode(file_get_contents($jsonPath), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

$hashTable = [];
foreach ($books as $book) {
    $key = md5(strtolower($book['title'] . ' ' . $book['author']));
    $hashTable[$key] = new Node($book);
}

function searchBook($query, $hashTable) {
    $query = strtolower($query);
    $results = [];
    foreach ($hashTable as $node) {
        $book = $node->data;
        if (
            strpos(strtolower($book['title']), $query) !== false ||
            strpos(strtolower($book['author']), $query) !== false
        ) {
            $results[] = $book;
        }
    }
    return $results;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$results = [];
if (!empty($search)) {
    $results = searchBook($search, $hashTable);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Library Hashtable Search</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    * {
        box-sizing: border-box;
        font-family: "Segoe UI", Arial, sans-serif;
    }
    body {
        background: linear-gradient(135deg, #8EC5FC, #E0C3FC);
        margin: 0;
        padding: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    h1 {
        color: #222;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    form {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        display: flex;
        gap: 10px;
        width: 100%;
        max-width: 450px;
    }
    input[type="text"] {
        flex: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
    }
    button {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    button:hover {
        background: linear-gradient(135deg, #2575fc, #6a11cb);
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
    }
    button:active {
        transform: scale(0.98);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }
    .results {
        margin-top: 25px;
        width: 100%;
        max-width: 600px;
    }
    .book {
        background: rgba(255, 255, 255, 0.9);
        padding: 18px 20px;
        border-radius: 12px;
        margin-bottom: 15px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        transition: transform 0.2s ease;
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }
    .book:hover {
        transform: scale(1.02);
    }
    .book img {
        width: 80px;
        height: auto;
        border-radius: 8px;
        object-fit: cover;
    }
    .book strong {
        color: #4A47A3;
    }
    .no-result {
        background: #fff;
        padding: 15px;
        border-radius: 10px;
        color: #777;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        text-align: center;
    }
    a {
        color: #2575fc;
        text-decoration: none;
        font-weight: 500;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
    <h1>📚 Library Hashtable Search</h1>
    <form method="get">
        <input type="text" name="search" placeholder="Search by title or author..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="fa fa-search"></i> Search</button>
    </form>

    <div class="results">
        <?php if (!empty($search)): ?>
            <h3>Search results for: "<?= htmlspecialchars($search) ?>"</h3>
            <?php if (empty($results)): ?>
                <p class="no-result">No matching books found.</p>
            <?php else: ?>
                <?php foreach ($results as $book): ?>
                    <div class="book">
                        <?php if (!empty($book['imageLink'])): ?>
                            <img src="<?= htmlspecialchars($book['imageLink']) ?>" alt="Book cover">
                        <?php endif; ?>
                        <div>
                            <strong>Title:</strong> <?= htmlspecialchars($book['title']) ?><br>
                            <strong>Author:</strong> <?= htmlspecialchars($book['author']) ?><br>
                            <strong>Language:</strong> <?= htmlspecialchars($book['language']) ?><br>
                            <strong>Year:</strong> <?= htmlspecialchars($book['year']) ?><br>
                            <?php if (!empty($book['link'])): ?>
                                <a href="<?= htmlspecialchars($book['link']) ?>" target="_blank">More info</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
