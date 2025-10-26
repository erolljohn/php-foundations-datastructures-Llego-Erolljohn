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

$booksFile = __DIR__ . '/books.json';
if (!file_exists($booksFile)) die("❌ Error: books.json not found.");
$booksData = json_decode(file_get_contents($booksFile), true);
if (!is_array($booksData)) die("❌ Error: Invalid books.json format.");

usort($booksData, fn($a, $b) => strcmp($a['title'], $b['title']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📚 My Digital Bookshelf</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #6aa9ff, #004aad);
  color: #f8fafc;
  margin: 0;
  padding: 40px;
  min-height: 100vh;
}
h1 {
  text-align: center;
  font-size: 2.5rem;
  color: #ffffff;
  margin-bottom: 10px;
  text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
}
.subtitle {
  text-align: center;
  color: #dbeafe;
  margin-bottom: 40px;
  font-size: 1.1rem;
}
.search-container {
  text-align: center;
  margin-bottom: 30px;
}
#searchInput {
  width: 65%;
  max-width: 500px;
  padding: 12px 16px;
  border: none;
  border-radius: 30px;
  font-size: 16px;
  outline: none;
  box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}
.book-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 25px;
  padding: 10px;
}
.card {
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(10px);
  border-radius: 15px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  text-align: center;
  padding: 15px;
  cursor: pointer;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 18px rgba(0,0,0,0.25);
}
.card img {
  width: 100%;
  height: 240px;
  object-fit: cover;
  border-radius: 12px;
  margin-bottom: 10px;
}
.card strong {
  display: block;
  font-size: 1.1em;
  color: #fff;
  margin-top: 10px;
}
.small {
  color: #dbeafe;
  font-size: 0.9em;
}
.no-results {
  text-align: center;
  color: #fff;
  font-size: 1.2em;
  margin-top: 40px;
}
.modal {
  display: none;
  position: fixed;
  z-index: 999;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  justify-content: center;
  align-items: center;
  backdrop-filter: blur(5px);
}
.modal-content {
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(15px);
  border-radius: 20px;
  padding: 25px;
  width: 90%;
  max-width: 420px;
  text-align: center;
  color: #fff;
  position: relative;
  box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}
.modal-content img {
  width: 100%;
  height: 260px;
  object-fit: cover;
  border-radius: 15px;
  margin-bottom: 20px;
}
.modal-content h2 {
  font-size: 1.6rem;
  margin-bottom: 10px;
}
.modal-content p {
  margin: 5px 0;
  font-size: 1rem;
}
.close-btn {
  position: absolute;
  top: 12px;
  right: 15px;
  background: #ef4444;
  border: none;
  color: white;
  font-size: 18px;
  padding: 6px 10px;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.2s;
}
.close-btn:hover {
  background: #b91c1c;
}
.more-info-btn {
  background: linear-gradient(135deg, #38bdf8, #2563eb);
  color: white;
  padding: 10px 18px;
  border: none;
  border-radius: 25px;
  font-size: 15px;
  cursor: pointer;
  margin-top: 15px;
  text-decoration: none;
  display: inline-block;
  transition: transform 0.2s ease, background 0.3s ease;
}
.more-info-btn:hover {
  transform: scale(1.05);
  background: linear-gradient(135deg, #2563eb, #1e40af);
}
</style>
</head>
<body>

<h1>✨ My Digital Bookshelf</h1>
<p class="subtitle">Explore timeless stories and classic literature from around the world 🌍</p>

<div class="search-container">
  <input type="text" id="searchInput" placeholder="🔍 Search your favorite book...">
</div>

<div class="book-grid" id="bookGrid">
<?php foreach ($booksData as $b): ?>
  <?php
  $imageLink = trim($b['imageLink'] ?? '');
  if ($imageLink === '' || !preg_match('/^https?:\/\//', $imageLink)) {
      $imageLink = 'https://via.placeholder.com/150x220?text=No+Image';
  }
  ?>
  <div class="card" 
       data-title="<?php echo strtolower(htmlspecialchars($b['title'])); ?>" 
       onclick='showBook(<?php echo htmlspecialchars(json_encode($b)); ?>)'>
    <img src="<?php echo htmlspecialchars($imageLink); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>">
    <strong><?php echo htmlspecialchars($b['title']); ?></strong>
    <div class="small">👤 <?php echo htmlspecialchars($b['author']); ?></div>
    <div class="small">📅 <?php echo htmlspecialchars($b['year']); ?></div>
  </div>
<?php endforeach; ?>
</div>

<p id="noResults" class="no-results" style="display:none;">❌ No books match your search.</p>

<div class="modal" id="bookModal">
  <div class="modal-content" id="modalContent">
    <button class="close-btn" onclick="closeModal()">✖</button>
    <img id="modalImage" src="" alt="Book Image">
    <h2 id="modalTitle"></h2>
    <p><strong>👤 Author:</strong> <span id="modalAuthor"></span></p>
    <p><strong>🌍 Country:</strong> <span id="modalCountry"></span></p>
    <p><strong>🗣️ Language:</strong> <span id="modalLanguage"></span></p>
    <p><strong>📖 Pages:</strong> <span id="modalPages"></span></p>
    <p><strong>📅 Published:</strong> <span id="modalYear"></span></p>
    <a id="modalLink" href="#" target="_blank" class="more-info-btn" style="display:none;">📘 Read More</a>
  </div>
</div>

<script>
function showBook(book) {
  document.getElementById('bookModal').style.display = 'flex';
  document.getElementById('modalTitle').textContent = book.title || 'Unknown';
  document.getElementById('modalAuthor').textContent = book.author || 'Unknown';
  document.getElementById('modalCountry').textContent = book.country || 'Unknown';
  document.getElementById('modalLanguage').textContent = book.language || 'Unknown';
  document.getElementById('modalPages').textContent = book.pages || '—';
  document.getElementById('modalYear').textContent = book.year || '—';
  document.getElementById('modalImage').src = book.imageLink && book.imageLink.startsWith('http')
    ? book.imageLink
    : 'https://via.placeholder.com/150x220?text=No+Image';
  if (book.link) {
    const link = document.getElementById('modalLink');
    link.style.display = 'inline-block';
    link.href = book.link;
  } else {
    document.getElementById('modalLink').style.display = 'none';
  }
}

function closeModal() {
  document.getElementById('bookModal').style.display = 'none';
}

window.onclick = function(e) {
  const modal = document.getElementById('bookModal');
  if (e.target == modal) modal.style.display = 'none';
}

document.getElementById('searchInput').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  const cards = document.querySelectorAll('.card');
  let visibleCount = 0;
  cards.forEach(card => {
    const title = card.getAttribute('data-title');
    if (title.includes(filter)) {
      card.style.display = 'block';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });
  document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
});
</script>

</body>
</html>
