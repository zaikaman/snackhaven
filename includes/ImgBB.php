<?php

class ImgBB {
    private $api_key;
    private $api_url = 'https://api.imgbb.com/1/upload';

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function upload($image_file) {
        // Kiểm tra file tồn tại
        if (!file_exists($image_file)) {
            throw new Exception('File không tồn tại');
        }

        // Đọc file dưới dạng base64
        $base64_image = base64_encode(file_get_contents($image_file));

        // Chuẩn bị data để gửi lên ImgBB
        $data = array(
            'key' => $this->api_key,
            'image' => $base64_image
        );

        // Khởi tạo CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Thực hiện request
        $response = curl_exec($ch);
        
        // Kiểm tra lỗi
        if(curl_errno($ch)) {
            throw new Exception('Lỗi CURL: ' . curl_error($ch));
        }
        
        curl_close($ch);

        // Parse JSON response
        $result = json_decode($response, true);

        // Kiểm tra kết quả
        if (!$result['success']) {
            throw new Exception('Lỗi upload ảnh: ' . ($result['error']['message'] ?? 'Unknown error'));
        }

        // Trả về URL của ảnh đã upload
        return $result['data']['url'];
    }
} 