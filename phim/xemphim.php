<?php
include 'connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nguon = $_GET['nguon'] ?? 'sanpham';

$phim = null;
$sql = '';

// Chọn đúng bảng và cột theo nguồn phim
if ($id > 0 && $nguon === 'sanpham') {

    $sql = "SELECT
                id_sp AS id,
                ten_sp AS ten,
                anh_sp AS anh,
                tomtat_sp AS tomtat,
                chitiet_sp AS chitiet,
                video_sp AS video
            FROM sanpham
            WHERE id_sp = ?";

} elseif ($id > 0 && $nguon === 'sanpham2') {

    $sql = "SELECT
                id_sp2 AS id,
                ten_sp2 AS ten,
                anh_sp2 AS anh,
                tomtat_sp2 AS tomtat,
                chitiet_sp2 AS chitiet,
                video_sp2 AS video
            FROM sanpham2
            WHERE id_sp2 = ?";

} elseif ($id > 0 && $nguon === 'sanpham3') {

    $sql = "SELECT
                id_sp3 AS id,
                ten_sp3 AS ten,
                anh_sp3 AS anh,
                tomtat_sp3 AS tomtat,
                chitiet_sp3 AS chitiet,
                video_sp3 AS video
            FROM sanpham3
            WHERE id_sp3 = ?";

}

// Truy vấn phim
if ($sql !== '') {
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $phim = $result->fetch_assoc();

        $stmt->close();
    }
}

// Hàm bảo vệ dữ liệu khi xuất HTML
function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Chuyển link YouTube thành link nhúng an toàn
function getYouTubeEmbedUrl($url) {
    $parts = parse_url(trim((string)$url));

    if (!$parts || empty($parts['host'])) {
        return '';
    }

    $host = strtolower($parts['host']);
    $path = $parts['path'] ?? '';
    $videoId = '';

    // Link youtu.be/VIDEO_ID
    if ($host === 'youtu.be') {
        $videoId = trim($path, '/');

    // Link YouTube chính thức
    } elseif (in_array($host, [
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'youtube-nocookie.com',
        'www.youtube-nocookie.com'
    ], true)) {

        if (preg_match(
            '~^/embed/([a-zA-Z0-9_-]{11})~',
            $path,
            $matches
        )) {
            $videoId = $matches[1];

        } elseif (preg_match(
            '~^/shorts/([a-zA-Z0-9_-]{11})~',
            $path,
            $matches
        )) {
            $videoId = $matches[1];

        } else {
            parse_str($parts['query'] ?? '', $query);
            $videoId = $query['v'] ?? '';
        }

    } else {
        return '';
    }

    if (!preg_match('/^[a-zA-Z0-9_-]{11}$/', $videoId)) {
        return '';
    }

    return 'https://www.youtube-nocookie.com/embed/' . $videoId;
}

// Xác định loại video
$video = trim((string)($phim['video'] ?? ''));
$youtubeUrl = getYouTubeEmbedUrl($video);
$videoType = 'none';
$videoUrl = '';

// Nếu là YouTube
if ($youtubeUrl !== '') {
    $videoType = 'youtube';
    $videoUrl = $youtubeUrl;

// Nếu là URL MP4 hoặc đường dẫn MP4 tương đối
} elseif (
    preg_match('~^https?://~i', $video)
    && preg_match('/\.mp4$/i', parse_url($video, PHP_URL_PATH) ?? '')
) {
    $videoType = 'mp4';
    $videoUrl = $video;

} elseif (
    preg_match('~^[a-zA-Z0-9_./-]+\.mp4(?:\?.*)?$~i', $video)
    && !str_contains($video, '..')
) {
    $videoType = 'mp4';
    $videoUrl = $video;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?= $phim ? e($phim['ten']) : 'Không tìm thấy phim' ?> </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #0b0b0b;
            color: white;
            font-family: Arial, sans-serif;
        }

        .page {
            width: 86.6%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 70px 0 60px;
        }

        .back {
            display: inline-block;
            margin-bottom: 25px;
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        .back:hover {
            color: #00a8ff;
        }

        .video-container {
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #000;
            border-radius: 12px;
            overflow: hidden;
        }

        .video-container video,
        .video-container iframe {
            display: block;
            width: 100%;
            height: 100%;
            border: none;
            background: #000;
        }

        .video-container video {
            object-fit: contain;
        }

        .no-video {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ccc;
            text-align: center;
            padding: 20px;
        }

        .no-video .play-icon {
            font-size: 55px;
            margin-bottom: 10px;
        }

        .movie-info {
            margin-top: 25px;
        }

        .movie-info h1 {
            font-size: 30px;
            margin: 0 0 15px;
        }

        .summary {
            color: #ddd;
            font-size: 16px;
            line-height: 1.8;
        }

        .detail {
            color: #bbb;
            font-size: 15px;
            line-height: 1.8;
            white-space: pre-line;
        }

        .poster {
            width: 150px;
            max-width: 100%;
            border-radius: 8px;
            margin: 10px 0 20px;
        }

        .error {
            padding: 30px;
            background: #1b1b1b;
            border-radius: 10px;
        }

        .error a {
            color: #00a8ff;
        }

        @media (max-width: 600px) {
            .page {
                width: 92%;
                padding-top: 20px;
            }

            .movie-info h1 {
                font-size: 23px;
            }
        }
    </style>
    
</head>
<body>

    <?php include("header.php"); ?>

    <div class="page">
        <a class="back" href="javascript:history.back()"> &#8592; Quay lại</a>

        <?php if (!$phim): ?>

            <div class="error">
                <h2>Không tìm thấy phim!</h2>
                <p>Phim không tồn tại hoặc đường dẫn không hợp lệ. </p>
                <a href="index.php">Quay về trang chủ</a>
            </div>

        <?php else: ?>

            <div class="video-container">
                <?php if ($videoType === 'youtube'): ?>

                    <!-- Phát video YouTube -->
                    <iframe
                        src="<?= e($videoUrl) ?>"
                        title="<?= e($phim['ten']) ?>"
                        loading="lazy"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen>
                    </iframe>

                <?php elseif ($videoType === 'mp4'): ?>

                    <!-- Phát video MP4 -->
                    <video
                        controls
                        playsinline
                        preload="metadata"
                        poster="<?= e($phim['anh']) ?>">

                        <source
                            src="<?= e($videoUrl) ?>"
                            type="video/mp4">

                        Trình duyệt của bạn không hỗ trợ phát video.
                    </video>

                <?php else: ?>

                    <div class="no-video">
                        <div class="play-icon">&#9658;</div>
                        <h3>Video chưa được cập nhật</h3>
                        <p>Phim này hiện chưa có đường dẫn video.</p>
                    </div>

                <?php endif; ?>

            </div>

            <div class="movie-info">

                <h1><?= e($phim['ten']) ?></h1>

                <?php if (!empty($phim['anh'])): ?>
                    <img
                        class="poster"
                        src="<?= e($phim['anh']) ?>"
                        alt="<?= e($phim['ten']) ?>">
                <?php endif; ?>

                <h3>Giới thiệu phim</h3>
                <p class="summary"> <?= nl2br(e($phim['tomtat'])) ?> </p>
                <h3>Nội dung chi tiết</h3>
                <div class="detail"> <?= nl2br(e($phim['chitiet'])) ?> </div>

            </div>

        <?php endif; ?>

    </div>

    <?php include("footer.php"); ?>

</body>
</html>
