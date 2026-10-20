if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}

// Cuộn lên đầu trang sau khi tải
window.addEventListener('load', function() {
    window.scrollTo(0, 0);
});

// XỬ LÝ MENU ACTIVE VÀ CHUYỂN TRANG
document.addEventListener('DOMContentLoaded', function() {
    // Lấy tất cả các link menu trong nav
    const menuLinks = document.querySelectorAll('nav a');
    
    console.log('Số lượng menu tìm thấy:', menuLinks.length);
    
    // Nếu không có menu thì thoát
    if (menuLinks.length === 0) {
        console.error('Không tìm thấy menu!');
        return;
    }
    
    // Lấy tên file hiện tại (VD: index.php, truyen-hinh.php, ...)
    const currentPage = window.location.pathname.split('/').pop();
    console.log('Trang hiện tại:', currentPage);
    
    // Hàm xóa class active khỏi tất cả menu
    function removeActiveClass() {
        menuLinks.forEach(link => {
            link.classList.remove('active');
        });
    }
    
    // Hàm active menu theo trang hiện tại
    function activeMenuByCurrentPage() {
        let hasActive = false;
        
        menuLinks.forEach(link => {
            const href = link.getAttribute('href');
            // Lấy tên file từ href (VD: index.php, truyen-hinh.php)
            const hrefPage = href ? href.split('/').pop() : '';
            
            // Nếu href trùng với trang hiện tại
            if (hrefPage === currentPage || 
                (currentPage === '' && hrefPage === 'index.php') ||
                (currentPage === 'index.php' && hrefPage === 'index.php')) {
                link.classList.add('active');
                hasActive = true;
                console.log('Active menu: ' + link.textContent);
            }
        });
        
        // Nếu không tìm thấy trang nào active (VD: trang chủ mặc định)
        if (!hasActive && menuLinks.length > 0) {
            // Kiểm tra nếu là trang chủ
            if (currentPage === '' || currentPage === 'index.php') {
                menuLinks[0].classList.add('active');
            }
        }
    }
    
    // Thêm sự kiện click cho từng menu - CHUYỂN TRANG
    menuLinks.forEach(link => {
        // Lấy href từ menu
        const href = link.getAttribute('href');
        
        link.addEventListener('click', function(event) {
            // Nếu href tồn tại và không phải rỗng
            if (href && href !== '#' && href !== 'javascript:void(0)') {
                // Cho phép chuyển trang bình thường
                console.log('Chuyển sang trang: ' + href);
                // Không dùng preventDefault() để trình duyệt tự chuyển
                return;
            }
            
            // Nếu không có href hợp lệ thì ngăn chặn
            event.preventDefault();
            console.log('Menu này chưa có liên kết: ' + this.textContent);
        });
    });
    
    // Active menu theo trang hiện tại
    activeMenuByCurrentPage();
});

// XỬ LÝ HEADER ĐỔI MÀU KHI CUỘN
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    if (header) {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
}); 



