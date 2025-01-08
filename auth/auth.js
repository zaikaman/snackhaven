document.addEventListener('DOMContentLoaded', function() {
    // Xử lý form đăng ký
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const formData = new FormData(this);
                const response = await fetch('register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                showAlert(result.success ? 'success' : 'error', result.message);
                
                if (result.success) {
                    registerForm.reset();
                }
            } catch (error) {
                showAlert('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
                console.error('Error:', error);
            }
        });
    }

    // Xử lý form đăng nhập
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const formData = new FormData(this);
                const response = await fetch('login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                showAlert(result.success ? 'success' : 'error', result.message);
                
                if (result.success && result.redirect) {
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                }
            } catch (error) {
                showAlert('error', 'Có lỗi xảy ra, vui lòng thử lại sau');
                console.error('Error:', error);
            }
        });
    }

    // Xử lý hiển thị/ẩn mật khẩu
    const togglePassword = document.querySelectorAll('.toggle-password');
    togglePassword.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
});

// Hiển thị thông báo sử dụng SweetAlert2
function showAlert(type, message) {
    Swal.fire({
        icon: type,
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
} 