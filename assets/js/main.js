// Xử lý form liên hệ
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            
            fetch('process_contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const alert = document.createElement('div');
                alert.className = `alert alert-${data.success ? 'success' : 'error'}`;
                alert.textContent = data.message;
                
                // Thêm alert vào đầu form
                contactForm.insertBefore(alert, contactForm.firstChild);

                // Nếu thành công, reset form
                if (data.success) {
                    contactForm.reset();
                    // Xóa alert sau 5 giây
                    setTimeout(() => {
                        alert.remove();
                    }, 5000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const alert = document.createElement('div');
                alert.className = 'alert alert-error';
                alert.textContent = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
                contactForm.insertBefore(alert, contactForm.firstChild);
            });
        });
    }
}); 

// Xử lý đăng xuất
$(document).on('click', '#logout-btn', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'auth/logout.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Hiển thị thông báo thành công
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    // Chuyển hướng về trang chủ
                    window.location.href = response.redirect;
                });
            }
        },
        error: function() {
            // Xử lý lỗi
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Có lỗi xảy ra khi đăng xuất. Vui lòng thử lại sau.',
                confirmButtonText: 'Đóng'
            });
        }
    });
}); 