<style>
    .error-container {
        text-align: center;
        padding: 100px 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .error-code {
        font-size: 120px;
        color: #ff6b6b;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .error-message {
        font-size: 24px;
        color: #333;
        margin-bottom: 30px;
    }

    .error-description {
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .back-home {
        display: inline-block;
        background: #ff6b6b;
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .back-home:hover {
        background: #ff5252;
        color: white;
    }
</style>

<div class="error-container">
    <div class="error-code">404</div>
    <h1 class="error-message">Không Tìm Thấy Trang</h1>
    <p class="error-description">
        Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc đã được di chuyển.
        Vui lòng kiểm tra lại đường dẫn hoặc quay về trang chủ.
    </p>
    <a href="<?php echo url(''); ?>" class="back-home">
        <i class="fas fa-home"></i>
        Về Trang Chủ
    </a>
</div> 