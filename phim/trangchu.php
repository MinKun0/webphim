    <?php
    require_once 'connect.php';

    // Lấy phim mới nhất từ bảng sanpham theo phân trang
    function getPhimbo($conn, $limit = 10, $offset = 0) {
        $limit = max(1, min(30, (int)$limit));

        $sql = "SELECT
                    id_sp AS id,
                    ten_sp AS ten,
                    anh_sp AS anh,
                    tomtat_sp AS tomtat,
                    'sanpham' AS nguon
                FROM sanpham
                ORDER BY RAND()
                LIMIT $limit";

        $result = $conn->query($sql);
        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    function getAnime($conn, $limit = 10, $offset = 0) {
        $limit = max(1, min(30, (int)$limit));

        $sql = "SELECT
                    id_sp2 AS id,
                    ten_sp2 AS ten,
                    anh_sp2 AS anh,
                    tomtat_sp2 AS tomtat,
                    'sanpham2' AS nguon
                FROM sanpham2
                ORDER BY RAND()
                LIMIT $limit";

        $result = $conn->query($sql);
        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    function getPhimle($conn, $limit = 10, $offset = 0) {
        $limit = max(1, min(30, (int)$limit));

        $sql = "SELECT
                    id_sp3 AS id,
                    ten_sp3 AS ten,
                    anh_sp3 AS anh,
                    tomtat_sp3 AS tomtat,
                    'sanpham3' AS nguon
                FROM sanpham3
                ORDER BY RAND()
                LIMIT $limit";

        $result = $conn->query($sql);
        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    // Lấy phim ngẫu nhiên từ cả hai bảng
    function getTrendingProducts($conn, $limit = 10) {
        $limit = max(1, min(30, (int)$limit));

        $sql = "
            SELECT *
            FROM (
                SELECT
                    id_sp AS id,
                    ten_sp AS ten,
                    anh_sp AS anh,
                    tomtat_sp AS tomtat,
                    'sanpham' AS nguon
                FROM sanpham

                UNION ALL

                SELECT
                    id_sp2 AS id,
                    ten_sp2 AS ten,
                    anh_sp2 AS anh,
                    tomtat_sp2 AS tomtat,
                    'sanpham2' AS nguon
                FROM sanpham2

                UNION ALL

                SELECT
                    id_sp3 AS id,
                    ten_sp3 AS ten,
                    anh_sp3 AS anh,
                    tomtat_sp3 AS tomtat,
                    'sanpham3' AS nguon
                FROM sanpham3
            ) AS all_movies
            ORDER BY RAND()
            LIMIT $limit
        ";

        $result = $conn->query($sql);
        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    // Lấy dữ liệu cho 4 mục
    $xuHuong = getTrendingProducts($conn, 10);
    $phimBo  = getPhimbo($conn, 10, 0);
    $phimLe  = getPhimle($conn, 10, 0);
    $anime   = getAnime($conn, 10, 0);

    $sections = [
        ['title' => 'Xu hướng gần đây', 'data' => $xuHuong],
        ['title' => 'Phim bộ nổi bật', 'data' => $phimBo, 'link' => 'danhsach.php?nguon=sanpham'],
        ['title' => 'Phim lẻ đặc sắc', 'data' => $phimLe, 'link' => 'danhsach.php?nguon=sanpham3'],
        ['title' => 'Anime nổi bật', 'data' => $anime, 'link' => 'danhsach.php?nguon=sanpham2']
    ];

    // Mã hóa dữ liệu trước khi hiển thị
    function e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
    ?>

    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Trang chủ</title>
        <link rel="stylesheet" href="css/trangchu.css">
    </head>
    <body>

    <main class="home-container">

        <?php foreach ($sections as $section): ?>
            <section class="section-card">

                <div class="section-header">
                    <h4 class="section-title">
                        <?php echo e($section['title']); ?>
                    </h4>

                    <?php if (isset($section['link'])): ?>
                        <a href="<?php echo e($section['link']); ?>"
                        class="view-more">
                            <span class="view-more-text">Xem thêm</span>
                            <span class="view-more-arrow">></span>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($section['data'])): ?>
                    <div class="slider-container">

                        <button class="nav-btn prev-btn" onclick="prevSlide()">&#10094;</button>

                        <div class="products-wrapper">
                            <div class="movie-track">

                                <?php foreach ($section['data'] as $product): ?>

                                    <div class="product">
                                        <a class="product-link"
                                        href="xemphim.php?id=<?php echo (int)$product['id']; ?>&amp;nguon=<?php echo e($product['nguon']); ?>"
                                        title="<?php echo e($product['ten']); ?>">

                                            <img
                                                src="<?php echo e($product['anh']); ?>"
                                                alt="<?php echo e($product['ten']); ?>"
                                                loading="lazy"
                                            >

                                            <div class="overlay"></div>

                                            <div class="title">
                                                <?php echo e($product['ten']); ?>
                                            </div>
                                        </a>
                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                        <button class="nav-btn next-btn" onclick="nextSlide()">&#10095;</button>

                    </div>

                <?php else: ?>

                    <div class="empty-message">Không có sản phẩm</div>

                <?php endif; ?>

            </section>

        <?php endforeach; ?>

    </main>

    <script>
    document.querySelectorAll('.slider-container').forEach(slider => {
        const wrapper = slider.querySelector('.products-wrapper');
        const track = slider.querySelector('.movie-track');
        const prevBtn = slider.querySelector('.prev-btn');
        const nextBtn = slider.querySelector('.next-btn');

        let position = 0;
        const moviesPerClick = 4;

        function getStep() {
            const firstMovie = track.querySelector('.product');

            if (!firstMovie) return 0;

            const trackStyle = getComputedStyle(track);
            const gap = parseFloat(trackStyle.gap) || 0;

            return firstMovie.getBoundingClientRect().width + gap;
        }

        function getMaxPosition() {
            return Math.max(0, track.scrollWidth - wrapper.clientWidth);
        }

        function updateSlider() {
            const maxPosition = getMaxPosition();

            position = Math.max(0, Math.min(position, maxPosition));

            track.style.transform = `translateX(-${position}px)`;

            prevBtn.disabled = position <= 0;
            nextBtn.disabled = position >= maxPosition - 1;

            prevBtn.classList.toggle('disabled', prevBtn.disabled);
            nextBtn.classList.toggle('disabled', nextBtn.disabled);
        }

        nextBtn.addEventListener('click', () => {
            position += getStep() * moviesPerClick;
            updateSlider();
        });

        prevBtn.addEventListener('click', () => {
            position -= getStep() * moviesPerClick;
            updateSlider();
        });

        // Cập nhật vị trí khi đổi kích thước màn hình
        window.addEventListener('resize', updateSlider);

        track.style.transition = 'transform 0.4s ease-in-out';

        updateSlider();
    });
    </script>

    </body>
    </html>
