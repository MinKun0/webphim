<?php
require_once 'connect.php';

// Xác định bảng phim cần hiển thị
$nguon = $_GET['nguon'] ?? 'sanpham';

$theLoai = [
    'sanpham' => [
        'ten' => 'Phim bộ nổi bật',
        'id' => 'id_sp',
        'ten_phim' => 'ten_sp',
        'anh' => 'anh_sp',
        'tomtat' => 'tomtat_sp'
    ],
    'sanpham2' => [
        'ten' => 'Anime nổi bật',
        'id' => 'id_sp2',
        'ten_phim' => 'ten_sp2',
        'anh' => 'anh_sp2',
        'tomtat' => 'tomtat_sp2'
    ],
    'sanpham3' => [
        'ten' => 'Phim lẻ đặc sắc',
        'id' => 'id_sp3',
        'ten_phim' => 'ten_sp3',
        'anh' => 'anh_sp3',
        'tomtat' => 'tomtat_sp3'
    ]
];

// Không cho phép truy vấn bảng tùy ý từ URL
if (!isset($theLoai[$nguon])) {
    $nguon = 'sanpham';
}

$config = $theLoai[$nguon];

// Phân trang
$limit = 18;
$page = max(1, (int)($_GET['page'] ?? 1));

// Đếm tổng số phim
$sqlCount = "SELECT COUNT(*) AS tong FROM {$nguon}";
$resultCount = $conn->query($sqlCount);
$tongPhim = $resultCount
    ? (int)$resultCount->fetch_assoc()['tong']
    : 0;

$tongTrang = max(1, (int)ceil($tongPhim / $limit));
$page = min($page, $tongTrang);
$offset = ($page - 1) * $limit;

// Lấy phim của trang hiện tại
$idCol = $config['id'];
$tenCol = $config['ten_phim'];
$anhCol = $config['anh'];
$tomtatCol = $config['tomtat'];

$sql = "SELECT
            `$idCol` AS id,
            `$tenCol` AS ten,
            `$anhCol` AS anh,
            `$tomtatCol` AS tomtat
        FROM `$nguon`
        ORDER BY `$idCol` DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

$phim = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $phim[] = $row;
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($config['ten']) ?></title>
    <link rel="stylesheet" href="css/trangchu.css">

    <style>
        body {
            margin: 0;
            background: #0b0b0b;
            color: white;
            font-family: Arial, sans-serif;
        }

        .list-page {
            padding: 35px 6.7%;
            min-height: 70vh;
        }

        .list-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
            padding-top: 30px;
        }

        .list-heading h1 {
            margin: 0;
            font-size: 28px;
        }

        .movie-count {
            color: #aaa;
            font-size: 14px;
        }

        .movie-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 22px 15px;
        }

        .movie-card {
            min-width: 0;
            overflow: hidden;
            border-radius: 8px;
            background: #181818;
            transition: transform .25s;
        }

        .movie-card:hover {
            transform: translateY(-5px);
        }

        .movie-card a {
            color: white;
            text-decoration: none;
        }

        .movie-card img {
            display: block;
            width: 100%;
            aspect-ratio: 3 / 4;
            object-fit: cover;
            background: #292929;
        }

        .movie-info {
            padding: 10px;
        }

        .movie-title {
            margin: 0 0 7px;
            font-size: 15px;
            line-height: 1.4;
        }

        .movie-description {
            margin: 0;
            color: #aaa;
            font-size: 12px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .movie-card:hover .movie-title {
            color: #ff6500;
        }

        .empty-message {
            padding: 50px 0;
            text-align: center;
            color: #aaa;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin: 35px 0 15px;
        }

        .pagination a,
        .pagination span {
            min-width: 38px;
            height: 38px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 12px;
            border-radius: 6px;
            background: #222;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .pagination a:hover,
        .pagination .active {
            background: #ff6500;
        }

        @media (max-width: 1100px) {
            .movie-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .movie-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 15px 10px;
            }

            .list-page {
                padding: 25px 4%;
            }

            .list-heading h1 {
                font-size: 23px;
            }
        }

        @media (max-width: 450px) {
            .movie-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>

</head>
<body>
 
<?php include("header.php"); ?>

<main class="list-page">

    <div class="list-heading">
        <h2><?= e($config['ten']) ?></h2>
        <div class="movie-count">
            Tổng cộng <?= $tongPhim ?> phim
        </div>
    </div>

    <?php if (!empty($phim)): ?>

        <div class="movie-grid">

            <?php foreach ($phim as $movie): ?>

                <div class="movie-card">
                    <a href="xemphim.php?id=<?= (int)$movie['id'] ?>&amp;nguon=<?= e($nguon) ?>"
                       title="<?= e($movie['ten']) ?>">

                        <img
                            src="<?= e($movie['anh']) ?>"
                            alt="<?= e($movie['ten']) ?>"
                            loading="lazy"
                        >

                        <div class="movie-info">
                            <h3 class="movie-title">
                                <?= e($movie['ten']) ?>
                            </h3>

                            <p class="movie-description">
                                <?= e($movie['tomtat']) ?>
                            </p>
                        </div>

                    </a>
                </div>

            <?php endforeach; ?>

        </div>

        <?php if ($tongTrang > 1): ?>
            <nav class="pagination" aria-label="Phân trang">

                <?php if ($page > 1): ?>
                    <a href="?nguon=<?= e($nguon) ?>&amp;page=<?= $page - 1 ?>">
                        &#10094;
                    </a>
                <?php endif; ?>

                <?php
                $start = max(1, $page - 2);
                $end = min($tongTrang, $page + 2);

                for ($i = $start; $i <= $end; $i++):
                ?>

                    <?php if ($i === $page): ?>
                        <span class="active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?nguon=<?= e($nguon) ?>&amp;page=<?= $i ?>">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>

                <?php endfor; ?>

                <?php if ($page < $tongTrang): ?>
                    <a href="?nguon=<?= e($nguon) ?>&amp;page=<?= $page + 1 ?>">
                        &#10095;
                    </a>
                <?php endif; ?>

            </nav>
        <?php endif; ?>

    <?php else: ?>

        <div class="empty-message">
            Chưa có phim trong mục này.
        </div>

    <?php endif; ?>

</main>

<?php include("footer.php"); ?>

</body>
</html>