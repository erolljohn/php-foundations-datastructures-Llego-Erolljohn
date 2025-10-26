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

$jsonData = file_get_contents("books.json");
$books = json_decode($jsonData, true);

$fiction = [];
$nonFiction = [];

foreach ($books as $book) {
    $title = strtolower($book["title"]);
    if (
        str_contains($title, "history") ||
        str_contains($title, "diary") ||
        str_contains($title, "science") ||
        str_contains($title, "philosophy") ||
        str_contains($title, "art") ||
        str_contains($title, "biography")
    ) {
        $book["type"] = "Non-Fiction";
        $nonFiction[] = $book;
    } else {
        $book["type"] = "Fiction";
        $fiction[] = $book;
    }
}

$library = [
    "Fiction" => $fiction,
    "Non-Fiction" => $nonFiction
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>📚 Library — Fiction & Non-Fiction</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #93c5fd, #3b82f6);
  color: #111827;
  margin: 0;
  display: flex;
  min-height: 100vh;
}
.sidebar {
  width: 220px;
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(10px);
  padding: 30px 20px;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
}
.sidebar h2 {
  color: white;
  font-size: 1.3em;
  margin-bottom: 30px;
  text-align: center;
}
.sidebar button {
  background: #2563eb;
  color: white;
  border: none;
  width: 100%;
  padding: 12px;
  margin-bottom: 12px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 1em;
  transition: background 0.3s ease, transform 0.2s ease;
}
.sidebar button:hover {
  background: #1d4ed8;
  transform: scale(1.05);
}
.sidebar button.active {
  background: #1e3a8a;
}
.main-content {
  flex: 1;
  padding: 40px;
}
h1 {
  color: #1e3a8a;
  text-align: center;
  margin-bottom: 20px;
}
.search-container {
  text-align: center;
  margin-bottom: 30px;
}
#searchInput {
  width: 60%;
  padding: 12px 15px;
  font-size: 16px;
  border: none;
  border-radius: 50px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  outline: none;
  transition: all 0.3s ease;
}
#searchInput:focus {
  box-shadow: 0 4px 14px rgba(59,130,246,0.4);
  transform: scale(1.03);
}
.category h2 {
  color: #1d4ed8;
  border-bottom: 3px solid #3b82f6;
  padding-bottom: 5px;
  margin-top: 40px;
  margin-bottom: 20px;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-align: left;
}
.book-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 25px;
}
.book-card {
  background: #f8fafc;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  overflow: hidden;
  text-align: center;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.book-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 18px rgba(59,130,246,0.3);
}
.book-card img {
  width: 100%;
  height: 250px;
  object-fit: cover;
}
.book-info {
  padding: 12px 10px 18px;
}
.book-info h3 {
  color: #1e3a8a;
  font-size: 1.05em;
  margin: 10px 0 6px;
}
.book-info p {
  color: #475569;
  font-size: 0.9em;
  margin: 2px 0;
}
.book-info a {
  display: inline-block;
  margin-top: 10px;
  background: #2563eb;
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  text-decoration: none;
  transition: background 0.25s ease;
}
.book-info a:hover {
  background: #1d4ed8;
}
.no-results {
  text-align: center;
  color: #475569;
  font-size: 1.1em;
  display: none;
}
footer {
  text-align: center;
  margin-top: 40px;
  color: #334155;
  font-size: 0.9em;
}
</style>
</head>
<body>
<div class="sidebar">
  <h2>📚 Library Menu</h2>
  <button class="active" onclick="filterCategory('all', this)">📘 All Books</button>
  <button onclick="filterCategory('fiction', this)">🧙 Fiction</button>
  <button onclick="filterCategory('non-fiction', this)">📖 Non-Fiction</button>
</div>
<div class="main-content">
  <h1>Library Collection</h1>
  <div class="search-container">
    <input type="text" id="searchInput" placeholder="🔍 Search title, author, or type (fiction/non-fiction)...">
  </div>
  <div id="libraryContent">
    <?php foreach ($library as $category => $books): ?>
      <div class="category"><h2><?php echo htmlspecialchars($category); ?></h2></div>
      <div class="book-grid">
        <?php foreach ($books as $book): ?>
          <div class="book-card" 
               data-title="<?php echo strtolower(htmlspecialchars($book['title'])); ?>"
               data-author="<?php echo strtolower(htmlspecialchars($book['author'])); ?>"
               data-type="<?php echo strtolower(htmlspecialchars($book['type'])); ?>">
            <img src="<?php echo htmlspecialchars(trim($book["imageLink"] ?? 'https://via.placeholder.com/150x220?text=No+Image')); ?>" alt="Book Image">
            <div class="book-info">
              <h3><?php echo htmlspecialchars($book["title"]); ?></h3>
              <p>👤 <?php echo htmlspecialchars($book["author"]); ?></p>
              <p>📅 <?php echo htmlspecialchars($book["year"]); ?></p>
              <p>🏷️ <?php echo htmlspecialchars($book["type"]); ?></p>
              <a href="<?php echo htmlspecialchars(trim($book["link"])); ?>" target="_blank">More Info ↗</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
  <p class="no-results" id="noResults">❌ No matching books found.</p>
</div>
<script>
const searchInput = document.getElementById('searchInput');
const cards = document.querySelectorAll('.book-card');
const noResults = document.getElementById('noResults');
const buttons = document.querySelectorAll('.sidebar button');
searchInput.addEventListener('input', filterBooks);
function filterBooks() {
  const filter = searchInput.value.toLowerCase();
  let visibleCount = 0;
  cards.forEach(card => {
    const title = card.getAttribute('data-title');
    const author = card.getAttribute('data-author');
    const type = card.getAttribute('data-type');
    if (title.includes(filter) || author.includes(filter) || type.includes(filter)) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });
  noResults.style.display = visibleCount === 0 ? 'block' : 'none';
}
function filterCategory(category, btn) {
  buttons.forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  let visibleCount = 0;
  cards.forEach(card => {
    const type = card.getAttribute('data-type');
    if (category === 'all' || type === category) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });
  noResults.style.display = visibleCount === 0 ? 'block' : 'none';
}
</script>
</body>
</html>
