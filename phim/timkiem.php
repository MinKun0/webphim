<?php
include("connect.php");

// Lấy từ khóa tìm kiếm
$keyword = trim($_GET['keyword'] ?? '');

// Khởi tạo danh sách phim
$products = [];
$result = false;

// Khi chưa nhập từ khóa: lấy ngẫu nhiên 14 phim từ 3 bảng
if ($keyword === '') {

    $sql = "SELECT ten_sp, anh_sp
            FROM (
                SELECT ten_sp, anh_sp
                FROM sanpham

                UNION ALL

                SELECT ten_sp2 AS ten_sp, anh_sp2 AS anh_sp
                FROM sanpham2

                UNION ALL

                SELECT ten_sp3 AS ten_sp, anh_sp3 AS anh_sp
                FROM sanpham3
            ) AS tat_ca_phim
            ORDER BY RAND()
            LIMIT 14";

    $result = $conn->query($sql);

} else {

    // Tìm kiếm phim theo tên trong cả 3 bảng
    $sql = "SELECT ten_sp, anh_sp
            FROM sanpham
            WHERE ten_sp LIKE ?

            UNION ALL

            SELECT ten_sp2 AS ten_sp, anh_sp2 AS anh_sp
            FROM sanpham2
            WHERE ten_sp2 LIKE ?

            UNION ALL

            SELECT ten_sp3 AS ten_sp, anh_sp3 AS anh_sp
            FROM sanpham3
            WHERE ten_sp3 LIKE ?

            ORDER BY ten_sp ASC";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $search = "%" . $keyword . "%";

        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();

        $result = $stmt->get_result();
    }
}

// Đưa kết quả truy vấn vào mảng $products
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm phim</title>
    <link rel="stylesheet" href="css/timkiem.css">
</head>

<body>

    <?php include("header.php"); ?>

    <main class="search-page">

        <!-- Thanh tìm kiếm -->
        <form action="timkiem.php" method="GET" class="search-bar-form">
            <div class="search-input-wrapper">

                <span class="search-icon">
                    <svg width="22" height="22"
                         viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"/>
                        <line x1="16.65" y1="16.65" x2="21" y2="21"/>
                    </svg>
                </span>

                <input
                    type="text"
                    name="keyword"
                    placeholder="Tìm kiếm nội dung giải trí..."
                    value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>"
                >

                <button type="submit" class="search-submit-btn">
                    Tìm kiếm
                </button>

            </div>
        </form>

        <?php if ($keyword === ''): ?>

            <!-- Phim xu hướng -->
            <div class="trending-section">
                <h4>Xu hướng gần đây</h4>

                <div class="slider-container">
                    <button type="button" class="nav-btn prev-btn">&#10094;</button>

                    <div class="products-wrapper">
                        <div class="container" id="productContainer">

                            <?php if (count($products) > 0): ?>
                                <?php foreach ($products as $phim): ?>

                                    <div class="product">

                                        <img
                                            src="<?= htmlspecialchars($phim['anh_sp'], ENT_QUOTES, 'UTF-8') ?>"
                                            alt="<?= htmlspecialchars($phim['ten_sp'], ENT_QUOTES, 'UTF-8') ?>"
                                        >

                                        <div class="overlay"></div>

                                        <div class="title">
                                            <?= htmlspecialchars($phim['ten_sp'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>

                                    </div>
                                <?php endforeach; ?>

                            <?php else: ?>
                                <p class="no-result">Chưa có phim để hiển thị.</p>
                            <?php endif; ?>

                        </div>
                    </div>

                    <button type="button" class="nav-btn next-btn">&#10095;</button>

                </div>

            </div>
            
        <?php else: ?>

            <!-- Kết quả tìm kiếm -->
            <section class="search-results">
                <h3>Kết quả tìm kiếm</h3>

                <div class="movie-list">

                    <?php if (count($products) > 0): ?>

                        <?php foreach ($products as $phim): ?>
                            <div class="movie-card">

                                <div class="movie-thumb">
                                    <img
                                        src="<?= htmlspecialchars($phim['anh_sp'], ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($phim['ten_sp'], ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                </div>

                                <div class="movie-title">
                                    <?= htmlspecialchars($phim['ten_sp'], ENT_QUOTES, 'UTF-8') ?>
                                </div>

                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="no-result">
                            Không tìm thấy phim phù hợp.
                        </p>

                    <?php endif; ?>

                </div>
            </section>
        <?php endif; ?>

    </main>

<script>
    let currentStartIndex = 0;  
    let productsPerPage = 6;
    let step = 4;  
    let totalProducts = <?php echo count($products); ?>;

    const container = document.getElementById('productContainer');
    const products = document.querySelectorAll('.product');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');

    // Hàm cập nhật vị trí cuộn
    function updateSlide() {
        if (totalProducts === 0) return;
        
        // Đảm bảo tất cả sản phẩm đều hiển thị, dùng transform để trượt khung nhìn
        products.forEach(product => {
            product.style.display = 'block';
            product.style.flex = `0 0 calc(${100/productsPerPage}% - ${(productsPerPage-1)*15/productsPerPage}px)`;
        });

        // Lấy chiều rộng của một sản phẩm cộng với khoảng cách gap (15px)
        const productItem = container.querySelector('.product');
        if (productItem) {
            const itemWidth = productItem.offsetWidth + 15;
            const moveDistance = currentStartIndex * itemWidth;
            
            // Dùng transform translateX để tạo hiệu ứng trượt mượt mà
            container.style.transform = `translateX(-${moveDistance}px)`;
        }
        
        // Cập nhật trạng thái nút Prev
        if (currentStartIndex === 0) {
            prevBtn.classList.add('disabled');
        } else {
            prevBtn.classList.remove('disabled');
        }
        
        // Cập nhật trạng thái nút Next
        if (currentStartIndex + productsPerPage >= totalProducts) {
            nextBtn.classList.add('disabled');
        } else {
            nextBtn.classList.remove('disabled');
        }
    }

    function prevSlide() {
        if (currentStartIndex > 0) {
            currentStartIndex = Math.max(0, currentStartIndex - step);
            updateSlide();
        }
    }

    function nextSlide() {
        if (currentStartIndex + productsPerPage < totalProducts) {
            // Tránh trượt lố bịch ở cuối danh sách
            currentStartIndex = Math.min(totalProducts - productsPerPage, currentStartIndex + step);
            updateSlide();
        }
    }

    // Khởi tạo hiển thị ban đầu
    if (totalProducts > 0) {
        container.style.display = 'flex';
        container.style.flexWrap = 'nowrap';
        container.style.gap = '15px';
        container.style.transition = 'transform 0.4s ease-in-out';
        
        updateSlide();
    }

    // Xử lý sự kiện click nút
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);

    // Xử lý resize màn hình
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Tùy chỉnh responsive số lượng sản phẩm hiển thị trên 1 trang tùy ý tại đây nếu cần
            updateSlide();
        }, 250);
    });
</script>

    <?php include("footer.php"); ?>

</body>
</html>
