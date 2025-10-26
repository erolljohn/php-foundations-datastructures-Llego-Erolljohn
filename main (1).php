<?php
function loadBooks() {
    $path = __DIR__ . '/books.json';
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    return json_decode($json, true);
}

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

class BST {
    public $root;
    public function __construct() { $this->root = null; }
    public function insert($data) { $this->root = $this->insertRec($this->root, $data); }
    private function insertRec($root, $data) {
        if ($root === null) return new Node($data);
        if (strcasecmp($data['title'], $root->data['title']) < 0)
            $root->left = $this->insertRec($root->left, $data);
        else $root->right = $this->insertRec($root->right, $data);
        return $root;
    }
    public function inorder($root, &$res) {
        if ($root) {
            $this->inorder($root->left, $res);
            $res[] = $root->data;
            $this->inorder($root->right, $res);
        }
    }
}

class HashTable {
    private $table;
    public function __construct() { $this->table = []; }
    private function hash($key) { return crc32(strtolower($key)) % 50; }
    public function insert($key, $value) {
        $i = $this->hash($key);
        $this->table[$i][] = $value;
    }
    public function search($key) {
        $i = $this->hash($key);
        if (isset($this->table[$i])) {
            foreach ($this->table[$i] as $item) {
                if (strcasecmp($item['title'], $key) == 0) return $item;
            }
        }
        return null;
    }
}

function countBooksRecursively($arr) {
    if (!$arr) return 0;
    array_pop($arr);
    return 1 + countBooksRecursively($arr);
}

$books = loadBooks();
$bst = new BST();
$hash = new HashTable();

foreach ($books as $b) {
    $bst->insert($b);
    $hash->insert($b['title'], $b);
}

$resultBooks = [];
if (isset($_GET['view'])) {
    $v = $_GET['view'];
    if ($v == 'bst') $bst->inorder($bst->root, $resultBooks);
    elseif ($v == 'hash' && isset($_GET['search'])) {
        $found = $hash->search($_GET['search']);
        if ($found) $resultBooks[] = $found;
    } elseif ($v == 'recursive') {
        $count = countBooksRecursively($books);
        $resultBooks = $books;
    } else $resultBooks = $books;
} else $resultBooks = $books;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Library Nexus v9</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    display: flex;
    height: 100vh;
    font-family: "Inter", sans-serif;
    background: linear-gradient(135deg, #0e0e10, #121424 70%);
    color: #f4f4f4;
}
.sidebar {
    width: 240px;
    background: #1c1f2e;
    border-right: 1px solid #2c3145;
    display: flex;
    flex-direction: column;
    padding: 30px 20px;
}
.sidebar h1 {
    color: #00e6b8;
    font-size: 1.5rem;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.sidebar a {
    text-decoration: none;
    color: #ddd;
    margin: 10px 0;
    padding: 10px;
    border-radius: 6px;
    transition: 0.3s;
}
.sidebar a:hover {
    background: #00e6b8;
    color: #000;
    font-weight: 600;
}
.search-box {
    margin-top: 20px;
}
.search-box input {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: none;
    outline: none;
    background: #2c3045;
    color: white;
}
.search-box button {
    width: 100%;
    margin-top: 8px;
    padding: 10px;
    border: none;
    background: #00e6b8;
    color: #000;
    font-weight: 700;
    border-radius: 6px;
    cursor: pointer;
}
main {
    flex: 1;
    padding: 30px;
    overflow-y: auto;
}
main h2 {
    color: #00ffc6;
    margin-bottom: 20px;
    font-size: 1.8em;
    text-shadow: 0 0 5px #00ffc6;
}
.books {
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
}
.book {
    background: #1f2234;
    border-radius: 15px;
    overflow: hidden;
    width: 210px;
    transition: 0.3s;
    box-shadow: 0 0 10px rgba(0,255,200,0.1);
    cursor: pointer;
}
.book:hover {
    transform: translateY(-5px);
    box-shadow: 0 0 15px rgba(0,255,200,0.4);
}
.book img {
    width: 100%;
    height: 280px;
    object-fit: cover;
}
.book .info {
    padding: 10px 12px;
}
.book h3 {
    font-size: 1em;
    color: #00ffc6;
    margin-bottom: 5px;
}
.book p {
    color: #aaa;
    font-size: 0.85em;
}
.modal-bg {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.75);
    display: none;
    align-items: center;
    justify-content: flex-end;
    z-index: 100;
}
.modal {
    width: 400px;
    height: 100%;
    background: #171a29;
    border-left: 2px solid #00e6b8;
    padding: 25px;
    overflow-y: auto;
    transform: translateX(100%);
    transition: transform 0.4s ease;
}
.modal.show {
    transform: translateX(0);
}
.modal h2 {
    color: #00ffc6;
    margin-bottom: 10px;
}
.modal p {
    margin-bottom: 10px;
}
.modal a {
    display: inline-block;
    margin-top: 15px;
    background: #00ffc6;
    color: #000;
    padding: 10px 18px;
    border-radius: 6px;
    font-weight: bold;
    text-decoration: none;
}
.close {
    position: absolute;
    right: 15px;
    top: 15px;
    background: none;
    border: none;
    color: #00ffc6;
    font-size: 1.5em;
    cursor: pointer;
}
footer {
    text-align: center;
    padding: 15px;
    color: #666;
    font-size: 0.9em;
    border-top: 1px solid #222;
    margin-top: 40px;
}
</style>
</head>
<body>
<div class="sidebar">
    <h1>Library</h1>
    <a href="?view=all">All Books</a>
    <a href="?view=bst">BST View</a>
    <a href="?view=recursive">Recursive View</a>
    <form class="search-box" method="get">
        <input type="text" name="search" placeholder="Search by title...">
        <input type="hidden" name="view" value="hash">
        <button type="submit">Search</button>
    </form>
</div>
<main>
    <h2>📚 Book Collection</h2>
    <div class="books">
        <?php if (!empty($resultBooks)): ?>
            <?php foreach ($resultBooks as $b): 
                $img = $b['imageLink'] ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/480px-No_image_available.svg.png';
                $desc = htmlspecialchars($b['description'] ?? 'No description available');
                $link = htmlspecialchars($b['link'] ?? '#');
            ?>
                <div class="book" 
                    data-title="<?= htmlspecialchars($b['title']) ?>"
                    data-author="<?= htmlspecialchars($b['author'] ?? 'Unknown') ?>"
                    data-year="<?= htmlspecialchars($b['year'] ?? 'N/A') ?>"
                    data-genre="<?= htmlspecialchars($b['genre'] ?? 'N/A') ?>"
                    data-desc="<?= $desc ?>"
                    data-link="<?= $link ?>">
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($b['title']) ?>">
                    <div class="info">
                        <h3><?= htmlspecialchars($b['title']) ?></h3>
                        <p><?= htmlspecialchars($b['author'] ?? 'Unknown') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No books found.</p>
        <?php endif; ?>
    </div>
</main>
<div class="modal-bg" id="modalBg">
    <div class="modal" id="modalPanel">
        <button class="close" onclick="closeModal()">×</button>
        <h2 id="mTitle"></h2>
        <p><strong>Author:</strong> <span id="mAuthor"></span></p>
        <p><strong>Year:</strong> <span id="mYear"></span></p>
        <p><strong>Genre:</strong> <span id="mGenre"></span></p>
        <p id="mDesc"></p>
        <a id="mLink" href="#" target="_blank">View More</a>
        
    </div>
</div>
<script>
const modalBg = document.getElementById('modalBg');
const modalPanel = document.getElementById('modalPanel');
const books = document.querySelectorAll('.book');
books.forEach(book => {
    book.addEventListener('click', () => {
        document.getElementById('mTitle').textContent = book.dataset.title;
        document.getElementById('mAuthor').textContent = book.dataset.author;
        document.getElementById('mYear').textContent = book.dataset.year;
        document.getElementById('mGenre').textContent = book.dataset.genre;
        document.getElementById('mDesc').textContent = book.dataset.desc;
        document.getElementById('mLink').href = book.dataset.link;
        modalBg.style.display = 'flex';
        setTimeout(() => modalPanel.classList.add('show'), 10);
    });
});
function closeModal() {
    modalPanel.classList.remove('show');
    setTimeout(() => { modalBg.style.display = 'none'; }, 300);
}
modalBg.addEventListener('click', e => {
    if (e.target === modalBg) closeModal();
});
</script>
</body>
</html>
