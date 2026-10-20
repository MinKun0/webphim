<!doctype html>
<?php include("connect.php")?>
<html>
<head>
    <meta charset="utf-8">
    <title>Untitled Document</title>
	<link rel="stylesheet" href="css/sanphamm.css">
</head>
<body>

    <div class="header">
        <h4>Top 10 phim bộ xu hướng</h4>
    </div>

    <div class="slider-container">
        <button class="nav-btn prev-btn" onclick="prevSlide()">&#10094;</button>
        
        <div class="products-wrapper">
            <div class="container" id="productContainer">
                <?php
                $sql = "SELECT * FROM sanpham3 LIMIT 10";
                $result = $conn->query($sql);
                
                $products = [];
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $products[] = $row;
                    }
                }
                
                // Hiển thị tất cả sản phẩm 
                foreach ($products as $product) {
                ?>
                    <div class="product">

                        <a class="product-link"
                        href="xemphim.php?id=<?php echo (int)$product['id_sp3']; ?>&nguon=sanpham3">

                            <img
                                src="<?php echo htmlspecialchars($product['anh_sp3']); ?>"
                                alt="<?php echo htmlspecialchars($product['ten_sp3']); ?>"
                            >

                            <div class="overlay"></div>

                            <div class="title">
                                <?php echo htmlspecialchars($product['ten_sp3']); ?>
                            </div>

                        </a>

                    </div>
                <?php
                }
                
                if (count($products) == 0) {
                    echo "<div style='color:white;text-align:center;padding:50px;'>Không có sản phẩm</div>";
                }
                ?>
            </div>
        </div>
        
        <button class="nav-btn next-btn" onclick="nextSlide()">&#10095;</button>
    </div>

<script>
    let currentStartIndex = 0;  // Vị trí bắt đầu hiển thị
    let productsPerPage = 6;
    let step = 4;  
    let totalProducts = <?php echo count($products); ?>;

    // Lấy container và các sản phẩm
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
        // Thêm hiệu ứng chuyển động mượt cho transform
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

</body>
</html>