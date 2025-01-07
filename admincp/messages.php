<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý cập nhật trạng thái tin nhắn
if(isset($_POST['update_status'])) {
    $message_id = $_POST['message_id'];
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
        $stmt->execute([$status, $message_id]);
        $success = "Đã cập nhật trạng thái tin nhắn";
    } catch(PDOException $e) {
        $error = "Lỗi khi cập nhật trạng thái: " . $e->getMessage();
    }
}

// Lấy danh sách tin nhắn
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

$page_title = 'Quản lý Tin nhắn';
$current_page = 'messages';
require_once 'includes/header.php';
?>

    <div class="container-fluid py-4">
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý Tin nhắn</h2>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Người gửi</th>
                                <th>Liên hệ</th>
                                <th>Tiêu đề</th>
                                <th>Trạng thái</th>
                                <th>Ngày gửi</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($messages as $message): ?>
                            <tr>
                                <td><?php echo $message['id']; ?></td>
                                <td><?php echo $message['name']; ?></td>
                                <td>
                                    Email: <?php echo $message['email']; ?><br>
                                    SĐT: <?php echo $message['phone']; ?>
                                </td>
                                <td><?php echo $message['subject']; ?></td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    $status_text = '';
                                    switch($message['status']) {
                                        case 'new':
                                            $status_class = 'bg-warning';
                                            $status_text = 'Mới';
                                            break;
                                        case 'read':
                                            $status_class = 'bg-info';
                                            $status_text = 'Đã đọc';
                                            break;
                                        case 'replied':
                                            $status_class = 'bg-success';
                                            $status_text = 'Đã trả lời';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info view-message" 
                                            data-id="<?php echo $message['id']; ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#messageDetailModal">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if($message['status'] == 'new'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                        <input type="hidden" name="status" value="read">
                                        <button type="submit" name="update_status" class="btn btn-sm btn-primary">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php if($message['status'] != 'replied'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                        <input type="hidden" name="status" value="replied">
                                        <button type="submit" name="update_status" class="btn btn-sm btn-success">
                                            <i class="bi bi-reply"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chi tiết tin nhắn -->
    <div class="modal fade" id="messageDetailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết tin nhắn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="messageDetail">
                        <!-- Nội dung chi tiết tin nhắn sẽ được load bằng AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Xử lý khi click nút xem chi tiết
    document.querySelectorAll('.view-message').forEach(button => {
        button.addEventListener('click', function() {
            const messageId = this.dataset.id;
            fetch(`get_message.php?id=${messageId}`)
                .then(response => response.json())
                .then(data => {
                    let statusText = '';
                    switch(data.status) {
                        case 'new':
                            statusText = 'Mới';
                            break;
                        case 'read':
                            statusText = 'Đã đọc';
                            break;
                        case 'replied':
                            statusText = 'Đã trả lời';
                            break;
                    }
                    
                    let html = `
                        <div class="mb-3">
                            <h6>Thông tin người gửi:</h6>
                            <p>Họ tên: ${data.name}<br>
                               Email: ${data.email}<br>
                               SĐT: ${data.phone || 'Không có'}</p>
                        </div>
                        <div class="mb-3">
                            <h6>Nội dung tin nhắn:</h6>
                            <p><strong>Tiêu đề:</strong> ${data.subject}</p>
                            <p>${data.message}</p>
                        </div>
                        <div class="mb-3">
                            <h6>Thông tin khác:</h6>
                            <p>Trạng thái: ${statusText}<br>
                               Thời gian: ${new Date(data.created_at).toLocaleString('vi-VN')}</p>
                        </div>`;
                    
                    document.getElementById('messageDetail').innerHTML = html;
                });
        });
    });
    </script>
</body>
</html> 